Invoice {{ $invoice->invoice_id }} from {{ \VentureDrake\LaravelCrm\Models\Setting::where('name', 'organisation_name')->first()->value }} for {{ $invoice->organisation->name  ?? null }} 