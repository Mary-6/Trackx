<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        $customers = Customer::withCount('shipments')
            ->search($request->get('q'))
            ->when($request->boolean('duplicates'), fn ($q) => $q->where('is_flagged_duplicate', true))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'q' => $request->get('q'),
        ]);
    }

    public function show(Customer $customer): View
    {
        $this->authorize('view', $customer);

        return view('admin.customers.show', [
            'customer' => $customer->load(['shipments.status', 'shipments.category']),
            'canSeeContact' => auth()->user()->can('viewContactDetails', $customer),
        ]);
    }

    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $old = $customer->only(array_keys($request->validated()));
        $customer->update($request->validated());

        AuditLogger::log('customer.updated', $customer, $old, $customer->only(array_keys($request->validated())));

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function toggleFlag(Customer $customer): RedirectResponse
    {
        $this->authorize('flag', $customer);

        $customer->update(['is_flagged_duplicate' => ! $customer->is_flagged_duplicate]);
        AuditLogger::log('customer.flag_toggled', $customer, null, ['is_flagged_duplicate' => $customer->is_flagged_duplicate]);

        return back()->with('success', $customer->is_flagged_duplicate ? 'Customer flagged for duplicate review.' : 'Duplicate flag cleared.');
    }

    public function export(): StreamedResponse
    {
        $this->authorize('export', Customer::class);

        AuditLogger::log('customer.exported');

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'City', 'Country', 'Shipments', 'Flagged Duplicate', 'Created']);

            Customer::withCount('shipments')->orderBy('name')->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $c) {
                    fputcsv($out, [
                        $c->id, $c->name, $c->email, $c->phone, $c->city, $c->country,
                        $c->shipments_count, $c->is_flagged_duplicate ? 'yes' : 'no',
                        $c->created_at->toDateTimeString(),
                    ]);
                }
            });

            fclose($out);
        }, 'customers-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }
}
