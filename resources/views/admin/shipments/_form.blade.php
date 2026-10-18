@php
    $s = $shipment ?? null;
    $v = fn (string $k, $default = '') => old($k, $s?->{$k} ?? $default);
    $req = '<span class="text-gold-600">*</span>';
    $in = 'mt-1 w-full rounded-lg border-navy-200 shadow-sm focus:border-gold-500 focus:ring-gold-500';
    $countries = App\Support\Countries::all();
    $countryOpts = function (string $current) use ($countries, $in) {
        $sel = '<select %s class="'.$in.'"><option value="">Select country…</option>';
        foreach ($countries as $c) {
            $sel .= '<option value="'.$c.'"'.($c === $current ? ' selected' : '').'>'.$c.'</option>';
        }
        if ($current !== '' && ! in_array($current, $countries, true)) {
            $sel .= '<option value="'.e($current).'" selected>'.e($current).'</option>';
        }
        return $sel.'</select>';
    };
@endphp

<div class="grid gap-8 lg:grid-cols-2">
    {{-- Core details --}}
    <section class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-navy-950">Shipment Details</h2>
        <p class="mt-1 text-xs text-navy-400">Fields marked {!! $req !!} are required — the tracking number is generated automatically when the shipment is created.</p>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            @isset($shipment)
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-navy-800">Tracking number</label>
                    <p class="mt-1 rounded-lg bg-navy-50 px-3 py-2 font-mono text-sm text-navy-800">{{ $s->tracking_number }}</p>
                </div>
            @endisset
            <div>
                <label class="text-sm font-medium text-navy-800">Category {!! $req !!}</label>
                <input name="category_name" value="{{ old('category_name', $s?->category?->name) }}" required placeholder="e.g. Watch, Documents…" class="{{ $in }}">
            </div>
            <div>
                <label class="text-sm font-medium text-navy-800">Title</label>
                <input name="title" value="{{ $v('title') }}" placeholder="Shipment registration" class="{{ $in }}">
            </div>
            @isset($statuses)
                <div>
                    <label class="text-sm font-medium text-navy-800">Status {!! $req !!}</label>
                    <select name="shipment_status_id" required class="{{ $in }}">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" @selected($status->slug === 'processing')>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-navy-800">Reason for consignment status</label>
                    <select name="status_reason" class="{{ $in }}">
                        <option value="">—</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->name }}" @selected($v('status_reason') === $status->name)>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endisset
            <div>
                <label class="text-sm font-medium text-navy-800">Product name</label>
                <input name="product_name" value="{{ $v('product_name') }}" class="{{ $in }}">
            </div>
            <div>
                <label class="text-sm font-medium text-navy-800">Weight (kg)</label>
                <input name="weight_kg" type="number" step="0.01" min="0" value="{{ $v('weight_kg') }}" class="{{ $in }}">
            </div>
            <div>
                <label class="text-sm font-medium text-navy-800">Total Shipment</label>
                <input name="quantity" type="number" min="1" value="{{ $v('quantity') }}" class="{{ $in }}">
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-navy-800">Description {!! $req !!}</label>
                <textarea name="description" rows="2" required class="{{ $in }}">{{ $v('description') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-navy-800">Departure Location</label>
                <input name="origin_city" value="{{ $v('origin_city') }}" placeholder="City, facility or address" class="{{ $in }}">
            </div>
            <div>
                <label class="text-sm font-medium text-navy-800">Departure Date</label>
                <input name="dispatch_date" type="date" value="{{ old('dispatch_date', $s?->dispatch_date?->toDateString()) }}" class="{{ $in }}">
            </div>
            <div>
                <label class="text-sm font-medium text-navy-800">Departure Time</label>
                <input name="departure_time" type="time" value="{{ $v('departure_time') }}" class="{{ $in }}">
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-navy-800">Pickup / Delivery Location</label>
                <input name="destination_city" value="{{ $v('destination_city') }}" placeholder="City or delivery area" class="{{ $in }}">
            </div>
            <div>
                <label class="text-sm font-medium text-navy-800">Pickup / Delivery Date</label>
                <input name="estimated_delivery_date" type="date" value="{{ old('estimated_delivery_date', $s?->estimated_delivery_date?->toDateString()) }}" class="{{ $in }}">
            </div>
            <div>
                <label class="text-sm font-medium text-navy-800">Pickup / Delivery Time</label>
                <input name="delivery_time" type="time" value="{{ $v('delivery_time') }}" class="{{ $in }}">
            </div>
            <div class="sm:col-span-2">
                <label class="text-sm font-medium text-navy-800">Current Location</label>
                <input name="current_location_name" value="{{ $v('current_location_name') }}" placeholder="e.g. London" class="{{ $in }}">
            </div>
        </div>
    </section>

    <div class="space-y-8">
        {{-- Sender --}}
        <section class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-navy-950">Sender Information</h2>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div><label class="text-sm font-medium text-navy-800">Name</label><input name="sender_name" value="{{ $v('sender_name') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">Email</label><input name="sender_email" type="email" value="{{ $v('sender_email') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">Phone</label><input name="sender_phone" value="{{ $v('sender_phone') }}" class="{{ $in }}"></div>
                <div class="sm:col-span-2"><label class="text-sm font-medium text-navy-800">Street address</label><input name="sender_address" value="{{ $v('sender_address') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">City</label><input name="sender_city" value="{{ $v('sender_city') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">State</label><input name="sender_state" value="{{ $v('sender_state') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">Country</label>{!! sprintf($countryOpts($v('sender_country')), 'name="sender_country"') !!}</div>
            </div>
        </section>

        {{-- Receiver --}}
        <section class="rounded-xl border border-navy-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-navy-950">Receiver Information</h2>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div><label class="text-sm font-medium text-navy-800">Name</label><input name="recipient_name" value="{{ $v('recipient_name') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">Email</label><input name="recipient_email" type="email" value="{{ $v('recipient_email') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">Phone</label><input name="recipient_phone" value="{{ $v('recipient_phone') }}" class="{{ $in }}"></div>
                <div class="sm:col-span-2"><label class="text-sm font-medium text-navy-800">Street address</label><input name="recipient_address" value="{{ $v('recipient_address') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">City</label><input name="recipient_city" value="{{ $v('recipient_city') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">State</label><input name="recipient_state" value="{{ $v('recipient_state') }}" class="{{ $in }}"></div>
                <div><label class="text-sm font-medium text-navy-800">Country</label>{!! sprintf($countryOpts($v('recipient_country')), 'name="recipient_country"') !!}</div>
            </div>
            <p class="mt-3 text-xs text-navy-400">Public visibility of each field is controlled in Settings → Public Tracking Page Privacy.</p>
        </section>
    </div>
</div>
