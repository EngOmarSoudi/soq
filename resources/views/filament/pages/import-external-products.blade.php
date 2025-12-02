<x-filament::page>
    <div class="grid grid-cols-1 gap-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Import External Products</h1>
            <p class="text-gray-500">Import products from external platforms like Temu, CJ Dropshipping, and Shein</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <x-filament::form wire:submit="importProducts">
                {{ $this->form }}
            </x-filament::form>
        </div>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-medium text-blue-800">Import Instructions</h3>
            <ul class="list-disc pl-5 mt-2 space-y-1 text-blue-700">
                <li>Select the platform you want to import products from</li>
                <li>For CJ Dropshipping, enter a keyword to search for products</li>
                <li>For Shein, you can optionally specify a category ID</li>
                <li>Set the page number and number of products per page</li>
                <li>Click "Import Products" to start the import process</li>
            </ul>
        </div>
    </div>
    
    @if (session()->has('notification'))
        <x-filament::notification :status="session('notification.type')" :title="session('notification.title')" />
    @endif
</x-filament::page>