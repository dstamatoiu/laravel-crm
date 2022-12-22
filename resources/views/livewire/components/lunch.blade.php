<li class="media">
    <div class="card w-100 mb-2">
        <div class="card-body">
            {{--<img src="..." class="mr-3" alt="...">--}}
            <div class="media-body">
                <h5 class="mt-0 mb-1">{{ $lunch->name }} @include('laravel-crm::livewire.components.partials.lunch.actions', ['lunch' => $lunch])</h5>
                @include('laravel-crm::livewire.components.partials.lunch.content', ['lunch' => $lunch])
            </div>
        </div>
    </div>
    @push('livewire-js')
        <script>
            $(document).ready(function () {
                $(document).on("change", "input[name='start_at']", function () {
                    @this.set('start_at', $(this).val());
                });

                $(document).on("change", "input[name='finish_at']", function () {
                    @this.set('finish_at', $(this).val());
                });
            });
        </script>
    @endpush
</li>

