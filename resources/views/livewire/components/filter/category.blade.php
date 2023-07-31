<div class="w-full md:flex grid gap-y-5 grid-cols-2 gap-x-5 mb-5">
    <div>
        <label for="brand" class="text-gray-500 text-sm">Merk</label>
        <select wire:model="brand" name="brand" id="brand" class="block w-full md:w-[10rem] p-2 rounded-lg text-sm border-2 " >
            @foreach ($filterBrands as $brand)
                <option value="{{$brand}}">{{$brand}}</option>                        
            @endforeach
        </select>
    </div>
    <div>
        <label for="price_from" class="text-gray-500 text-sm">Prijs Vanaf</label>
        <div class="relative">
            <i class="fa-solid fa-euro absolute text-sm top-1/2 -translate-y-1/2 left-2"></i>
            <input wire:model="price_from" name="price_from" class=" block pl-5 w-full md:w-[10rem] p-2 rounded-lg text-sm border-2 " >
        </div>
    </div>
    <div>
        <label for="price_to" class="text-gray-500 text-sm">Prijs Tot</label>
        <div class="relative">
            <i class="fa-solid fa-euro absolute text-sm top-1/2 -translate-y-1/2 left-2"></i>
            <input wire:model="price_to" name="price_to" class=" block pl-5 w-full md:w-[10rem] p-2 rounded-lg text-sm border-2 " >
        </div>
    </div>
    <div class="flex gap-x-2 ml-auto">
        <button class="bg-accent text-white hover:bg-opacity-70 font-semibold h-[48px] mt-auto rounded w-[10rem] hidden md:block" wire:click="filter">
            Filter Resultaten
        </button>
        <button class="bg-accent2 text-white hover:bg-opacity-70 font-semibold h-[48px] mt-auto rounded px-4 hidden md:block" wire:click="clear">
            <i class="fa-solid fa-trash"></i>                    
        </button>
    </div>
    <button class="bg-accent2 text-white hover:bg-opacity-70 font-semibold h-[48px] mt-auto rounded px-4 md:hidden block" wire:click="clear">
        <i class="fa-solid fa-trash"></i>                    
    </button>
    <button class="bg-accent text-white hover:bg-opacity-70 font-semibold h-[48px] mt-auto rounded px-4 md:hidden block" wire:click="clear">
        Filter Resultaten
    </button>
</div>