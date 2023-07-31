<li  class="w-full p-3 hover:bg-mb xl:w-auto xl:p-0 xl:hover:bg-transparent cursor-pointer" @click.away="categories = false" ><span click="categories = ! categories" class="hover:bg-mb xl:p-2 xl:cursor-pointer xl:rounded-lg xl:px-3 w-full " @click="categories = ! categories">Categories <i x-show="!categories" class="fa-solid fa-angle-down text-sm ml-2"></i><i x-show="categories" class="fa-solid fa-angle-up text-sm ml-2"></i></span>
    <ul class="grid xl:mt-6 left-2 xl:left-0 shadow-lg absolute min-w-[10rem] text-sm -ml-2 rounded-b-lg pb-2 z-50" x-cloak x-show="categories">
        
        @foreach ($categories as $key => $value)

            <li x-data="{ subnav:false }" @click.away="subnav = false" class="hover:bg-mb bg-white relative flex cursor-pointer ">
                

                <a @if(count($value) == 0) href="{{route('category', ['category' => $key])}}" @endif class="w-full h-full p-2 px-4" @click="subnav = ! subnav">{{$key}} @if(count($value) != 0)<i x-show="categories" class="fa-solid fa-caret-up rotate-90 text-sm ml-2"></i>@endif</a>

                
                @if(count($value) != 0)
                    
                    <ul x-show="subnav" class=" absolute -right-[10rem] top-0 bg-white min-w-[10rem] border-t border-l">
                            
                        @foreach ($value as $item)

                            <a href="{{route('category', ['category' => str_replace(' ', '-', $item->name)])}}">
                                <li class="hover:bg-mb bg-white  p-2 px-4 cursor-pointer ">{{$item->name}}</li>
                            </a>

                        @endforeach

                    </ul>

                @endif

            </li>

        @endforeach
    

    </ul>
</li>