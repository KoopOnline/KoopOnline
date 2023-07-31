<div x-data="{search: false}">
    <div class="relative flex">
        <i class="fa-solid fa-search absolute top-1/2 -translate-y-1/2 left-2 text-gray-400"></i>
        <input wire:model="search" @click="search = true" @click.away="search = false" class=" w-full p-3  focus:border-accent2 focus:outline-none border-2 border-black border-opacity-30 rounded-lg pl-7" type="text" placeholder="Zoek naar uw favorite merk...">
        <button class="bg-accent px-5 rounded-lg ml-3"><i class="fa-solid fa-search  text-white"></i>
        </button>
    </div>
    @if($count != 0)
        <ul class="w-full bg-white rounded-lg mt-3 absolute shadow-lg cursor-pointer border-2 max-h-[20rem] overflow-y-auto custom-scrollbar" x-show="search">
            @foreach ($brands as $brand)
                <li>
                    <a href="{{route('brand', ['brand' => $brand['brand']])}}" class="text-gray-600 px-3 py-1.5 hover:bg-gray-100">
                        {{$brand['brand']}}
                    </a> 
                </li>
            @endforeach
            {{-- @if($count)
                <li class=" px-3 py-1 hover:bg-gray-100 text-sm text-center italic text-gray-400">
                    Bekijk de overige {{$count}} resultaten...
                </li>
            @endif --}}
        </ul>
    @endif
</div>