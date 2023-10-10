<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between">

            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Customers') }}
        </h2>
        {{-- <button x-data x-on:click="dispatch('toggle', 'csv-importer')">Import</button> --}}

    </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="px-2">
                    @foreach ($customers as $customer)
                        <div>
                            {{ $customer->id }}. {{  $customer->first_name }}  {{  $customer->first_name }}
                        </div>
                        <br>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
        <livewire:csv-importer :model="App\Models\Customer::class" :columnsToMap="['id','first_name','last_name','email',]"
        :requiredColumns="['id','first_name','last_name','email']" :columnLabels="['id'=>'ID','first_name'=>'First Name', 'last_name'=>'Last Name', 'email'=>'Email']"
        />
    </div>
</x-app-layout>
