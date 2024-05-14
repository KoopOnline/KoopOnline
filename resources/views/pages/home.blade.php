@extends('layouts.app')
@section('content')
    <div class="2xl:w-[1500px] mx-auto relative px-5">
        {{-- <div class="lg:block hidden">
            <div class="flex w-full mt-10 bg-cover bg-no-repeat p-5 h-[60vh] rounded-lg" style="background-image: url('{{asset('imgs/header2.jpg')}}'); ">
            
                <img src="https://www.kooponline.com/logo.png" alt="kooponline logo" class="h-[8rem]">             
    
            </div>
            <div class="py-10 my-auto rounded-lg flex relative mt-4">
                <div class="relative">
                    <div class="w-3/4">
                        <h2 class="font-bold text-xl mb-2 border-b-2 border-black border-opacity-10 pb-2"><span class="text-accent">Koop</span> <span class="text-accent2">Online</span></h2>
                        <h3 class=" text-gray-500 tracking-wider">Vergelijk prijzen bij KoopOnline.com! Sinds 2008 zijn wij de nummer één prijsvergelijkingssite voor de nieuwste en meest populaire producten! U kunt bij ons de beste smartphones, tofste drones, grootste koelkasten, hipste zonnebrillen en de heetse barbecues vinden. Producten voor uw mancave, kantoor, tuin, keuken, slaapkamer en badkamer hebben wij ook opgenomen in onze vergelijkingtool. Het online kopen van goederen was nog nooit zo makkelijk!</h3>
                    </div>
                    <div class=" top-1/2 -translate-y-1/2 absolute right-0 bg-white p-2 rounded-lg h-[5rem] cursor-pointer">
                        <h3 class=" ml-6 font-semibold absolute text-gray-500 text-sm">Download Onze App</h3>
                        <img class=" h-[8rem] -mt-5 " src="https://download.logo.wine/logo/Google_Play/Google_Play-Logo.wine.png" alt="">
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="grid gap-y-32 mt-20">

            @foreach ($productRows as $key => $row)
                
                <div>
                    <h1 class="font-bold text-2xl text-gray-500 border-b-2 border-opacity-10 pb-2 border-black"><span class="text-accent">{{$key}}</span> vergelijken</h1>
                    <div class="w-full grid sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-5 gap-10 mt-10">

                        @foreach ($row['products'] as $item)

                            <div class="bg-white p-4 rounded-lg flex flex-col justify-center items-center">
                                <h1 class="text-lg font-semibold text-center text-gray-500 overflow-hidden h-[56px]">{{$item->name}}</h1>
                                <img class="mx-auto mt-5 h-[120px]" src="https://www.kooponline.com/imageCache.php?src={{base64_encode($item->image_url)}}" alt="">
                                <h2 class="text-center text-sm text-gray-500 mt-2"><span class="italic">Vanaf</span> €{{$item->price}}</h2>
                                <a href="{{route('product', ['product_name' => str_replace(' ', '-', $item->normalised_name)])}}" class="bg-accent2 mt-6 mx-auto text-white hover:bg-opacity-70 font-semibold p-2 rounded">
                                    @if($item->count == 1) 
                                        Meer Informatie
                                    @else 
                                        Vergelijk {{$item->count}} Prijzen
                                    @endif
                                </a>
                            </div>
                
                        @endforeach
                    
                    </div>
                    
                    <div class="cols-span-5">
                        <a href="{{route('category', ['category' => str_replace(' ', '-', $key)])}}" class="float-right mt-4 text-sm text-gray-400 cursor-pointer">Vergelijk {{$key}} <i class="fa-solid fa-arrow-right text-accent"></i></a>
                    </div>
                </div>

            @endforeach

        </div>

        <div class="mb-24 mt-24">
            <div class="py-10 my-auto rounded-lg flex relative mt-4">
                <div class="relative">
                    <div class="w-full">
                        <h2 class="font-bold text-xl mb-2 border-b-2 border-black border-opacity-10 pb-2">
                            <span class="text-accent">Prijzen</span> <span class="text-accent2">Vergelijken</span>
                        </h2>
                        <p class=" text-gray-500 tracking-wider">
                            Vergelijk prijzen bij KoopOnline.com! Sinds 2008 zijn wij de nummer één prijsvergelijkingssite voor de nieuwste en meest populaire producten! Hier kunt u moeiteloos producten vergelijken om ervoor te zorgen dat u altijd het goedkoopst uit bent.<br /><br />
                            Het vergelijken van producten is nog nooit zo eenvoudig geweest. Of u nu op zoek bent naar elektronica, kleding, of huishoudelijke apparaten, onze website biedt een uitgebreide database van producten met actuele prijsinformatie. Met slechts een paar klikken kunt u verschillende opties vergelijken en de beste deals ontdekken.<br /><br />
                            Onze missie is om u tijd en geld te besparen. Door de mogelijkheid te bieden om prijzen te vergelijken, kunt u er zeker van zijn dat u de juiste beslissing neemt bij het kopen van een product. Of het nu gaat om een gadget, meubelstuk of een nieuw paar schoenen, wij helpen u de beste prijs te vinden.<br /><br />
                            Het kopen van producten via onze prijsvergelijkingswebsite is niet alleen eenvoudig, maar het biedt u ook gemoedsrust. U weet dat u de meest kosteneffectieve keuze maakt, zonder concessies te doen aan kwaliteit. Onze site is ontworpen met het oog op gebruiksgemak, zodat u snel de informatie kunt vinden die u nodig heeft om een weloverwogen beslissing te nemen.<br /><br />
                            Wij streven ernaar om uw go-to bron te zijn voor het vinden van de goedkoopste deals op het internet. Of u nu op zoek bent naar specifieke merken, modellen of gewoon de laagste prijs, onze prijsvergelijkingswebsite staat voor u klaar.<br /><br />
                            Wacht niet langer! Ontdek vandaag nog de voordelen van onze prijsvergelijkingswebsite en ervaar hoe eenvoudig het is om te vergelijken, kopen en het goedkoopst uit te zijn. Bespaar tijd en geld met slechts een paar klikken!
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
