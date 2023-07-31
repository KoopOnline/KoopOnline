<div class="xl:top-1/2 xl:-translate-y-1/2 xl:absolute xl:right-0 p-3 xl:p-0 z-50" x-data="{search: false}">
    <div class="flex">
        <div class="relative w-full">
            <i class="fa-solid fa-search absolute top-1/2 -translate-y-1/2 left-2 text-gray-400"></i>
            <input wire:model="search" @click="search = true" @click.away="search = false" class="xl:w-[22rem] p-3 pl-7 xl:p-2 w-full  focus:border-accent2 focus:outline-none xl:h-[2.7rem] border-2 border-black border-opacity-10 xl:border-opacity-30 rounded-lg xl:pl-7" type="text" placeholder="Begin met zoeken...">
        </div>
        <button wire:click="searchProduct" class="bg-accent px-4 rounded-lg ml-2"><i class="fa-solid fa-search  text-white"></i></button>
    </div>
    @if($count != 0)
        <ul class="w-full bg-white rounded-lg mt-3 mr-2 absolute shadow-lg cursor-pointer border-2 max-h-[20rem] overflow-y-auto custom-scrollbar z-40" x-show="search">
            @foreach ($products as $product)
                
            <a href="{{route('product', ['product_name' => $product['name']])}}" >
                <li class="text-gray-600 px-3 py-1.5 w-full hover:bg-gray-100 " >
                    {{$product['name']}}
                </li> 
            </a>

            @endforeach
            @if($count)
                <li class=" px-3 py-1 hover:bg-gray-100 text-sm text-center italic text-gray-400">
                    <a href="{{route('search', ['search' => $search])}}">
                        Bekijk de overige {{$count}} resultaten...
                    </a>
                </li>
            @endif
        </ul>
    @endif
</div>