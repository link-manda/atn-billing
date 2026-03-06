<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Products</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Manage software products and pricing.</p>
        </div>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">
            Add Product
        </flux:button>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <flux:callout variant="success" icon="check-circle">{{ session('success') }}</flux:callout>
    @endif

    {{-- Search --}}
    <flux:input wire:model.live.debounce.400ms="search" placeholder="Search products..." />

    {{-- Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Product Name</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Code</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Base Price</th>
                    <th class="text-left px-4 py-3 font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($products as $product)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/40 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-zinc-900 dark:text-white">{{ $product->product_name }}</p>
                            @if($product->description)
                                <p class="text-xs text-zinc-400 truncate max-w-xs">{{ $product->description }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <flux:badge variant="outline" size="sm">{{ $product->product_code }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                            Rp {{ number_format($product->base_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($product->status === 'active')
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Inactive</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button wire:click="openEdit({{ $product->id }})" size="sm" variant="ghost" icon="pencil-square" />
                                <flux:button wire:click="delete({{ $product->id }})"
                                    wire:confirm="Are you sure you want to delete this product?"
                                    size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-zinc-400">
                            <flux:icon.cube class="w-8 h-8 mx-auto mb-2 opacity-40" />
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>{{ $products->links() }}</div>

    {{-- Modal --}}
    <flux:modal wire:model="showModal" class="md:w-96 space-y-4">
        <flux:heading size="lg">{{ $editing_id ? 'Edit Product' : 'New Product' }}</flux:heading>

        <form wire:submit="save" class="space-y-4">
            <flux:field>
                <flux:label>Product Name <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <flux:input wire:model="product_name" placeholder="e.g. SI-PRIMA Clinic Basic" />
                <flux:error name="product_name" />
            </flux:field>

            <flux:field>
                <flux:label>Product Code <flux:badge size="sm" variant="outline">Required</flux:badge></flux:label>
                <flux:input wire:model="product_code" placeholder="e.g. SIPRIMA-BASIC" />
                <flux:error name="product_code" />
            </flux:field>

            <flux:field>
                <flux:label>Base Price (Rp)</flux:label>
                <flux:input wire:model="base_price" type="text" inputmode="decimal" placeholder="0,00" />
                <flux:error name="base_price" />
            </flux:field>

            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea wire:model="description" placeholder="Product description..." rows="3" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <select wire:model="status"
                    class="w-full rounded-lg border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 shadow-xs h-10 px-3 text-sm">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showModal', false)" variant="ghost" type="button">Cancel</flux:button>
                <flux:button variant="primary" type="submit">{{ $editing_id ? 'Update' : 'Create' }}</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
