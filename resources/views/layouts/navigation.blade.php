<nav class="z-40" x-data="{ categories: false, partners: false}">

    <div class="bg-sb w-full h-fit p-2  shadow-md hidden xl:block z-50">

        <div class=" py-4 2xl:w-[1500px] mx-auto relative ">
            <a href="{{route('home')}}" class="flex gap-x-2 absolute left-0 -mt-1 cursor-pointer ">
                <img src="{{asset('imgs/logo.PNG')}}" alt="" class="h-[2rem]">
                <span class="text-2xl font-semibold tracking-wider"><span class="text-accent">KOOP</span><span class="text-accent2">ONLINE</span></span>
            </a>
            <ul class="flex gap-x-8 relative w-fit text-opacity-70 text-black xl:mx-auto font-semibold" >
     
                <livewire:components.nav-categories/>
    
                <li><a href="{{route('brands')}}" class="hover:bg-mb p-2 cursor-pointer rounded-lg px-3">Merken</a></li>
                <li><a href="{{route('sellers')}}" class="hover:bg-mb p-2 cursor-pointer rounded-lg px-3">Verkopers</a></li>
                <li class="" @click.away="partners = false" @click="partners = ! partners"><span class="hover:bg-mb p-2 cursor-pointer rounded-lg px-3">Partners <i x-show="!partners" class="fa-solid fa-angle-down text-sm ml-2"></i><i x-show="partners" class="fa-solid fa-angle-up text-sm ml-2"></i></span>
                    <ul class="grid gap-y-2 mt-6 absolute min-w-[10rem] text-sm z-40 shadow-lg bg-white -ml-2 rounded-b-lg pb-2" x-cloak x-show="partners">
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer border-t "><a href="">Justlin Company</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a href="">Webwinkels</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a href="">Webshops</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a href="">Elektronicawebwinkel</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a href="">Zorgverzekering vergelijken 2019</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a href="">Energie vergelijken</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a href="">Gameserver Huren</a></li>
    
    
                    </ul>
                </li>            
    
            </ul>
            <livewire:components.product-nav-search/>
        </div>
    </div>
    
    <div class="xl:hidden block min-h-[5rem]  w-full" x-data="{ nav: false}">
    
        <div class="w-full bg-white h-fit p-4 text-center">
            <i @click="nav = true" x-show="!nav" class="fa-solid fa-bars absolute left-4 top-4 text-2xl text-gray-400 cursor-pointer"></i>
            <i @click="nav = false" x-show="nav" class="fa-solid fa-x absolute left-4 top-4 text-2xl text-gray-400 cursor-pointer"></i>
            <a href="{{route('home')}}" class="cursor-pointer ">
                <span class="text-2xl font-semibold tracking-wider"><span class="text-accent">KOOP</span><span class="text-accent2">ONLINE</span></span>
            </a>
        </div>
    
        <div class="relative  z-50 w-full" x-show="nav" x-cloak>

            <ul class="absolute bg-white w-full border-t rounded-b-lg shadow-lg" >

                <livewire:components.nav-categories/>
                <a href="{{route('brands')}}">
                    <li class="w-full p-3 hover:bg-mb cursor-pointer">
                        Merken
                    </li>
                </a>
                <a href="{{route('sellers')}}">
                    <li class="w-full p-3 hover:bg-mb cursor-pointer">
                        Verkopers
                    </li>
                </a>
                <li class="w-full p-3 hover:bg-mb cursor-pointer" @click="partners = ! partners">
                    Partners <i x-show="!partners" class="fa-solid fa-angle-down text-sm ml-2"></i><i x-show="partners" class="fa-solid fa-angle-up text-sm ml-2"></i>
                    <ul class="grid gap-y-2 xl:mt-6 absolute min-w-[10rem] text-sm z-40 shadow-lg bg-white -ml-2 rounded-b-lg pb-2" x-cloak x-show="partners">
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer border-t "><a target="_blank" href="https://www.justlin.nl/">Justlin Company</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a target="_blank" href="https://webwinkels.linkplein.net/">Webwinkels</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a target="_blank" href="https://webshops.linkdirectory.be/">Webshops</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a target="_blank" href="https://elektronicawebwinkel.webwinkelstart.nl/">Elektronicawebwinkel</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a target="_blank" href="https://www.verzekeringsvergelijker.com/">Zorgverzekering vergelijken 2019</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a target="_blank" href="https://www.deenergievergelijker.com/">Energie vergelijken</a></li>
                        <li class="hover:bg-mb p-2 px-4 cursor-pointer  "><a target="_blank" href="https://www.gameserverhuren.nl/">Gameserver Huren</a></li>
    
    
                    </ul>
                </li>

            </ul>

        </div>
        <livewire:components.product-nav-search/>
    
    </div>
    
</nav>
