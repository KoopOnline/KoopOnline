@extends('layouts.app')
@section('content')

    <div class="xl:w-[1500px] mx-auto relative grid gap-y-10 mt-10 px-10">

        <div class=" my-auto rounded-lg flex relative mt-4">
            <div class="relative">
                <div class="w-full">
                    <h2 class="font-bold text-xl mb-2 border-b-2 border-black border-opacity-10 pb-2"><span class="text-accent">Product</span> <span class="text-accent2">Verkopers</span></h2>
                    <h3 class=" text-gray-500 tracking-wider">Ontdek hier een diverse lijst van onze verkopers. Van bekende merken tot unieke ambachtelijke producten en opkomende lokale ondernemers. Blader door de lijst, laat u inspireren en klik op een verkoper om hun aanbod te ontdekken. We ondersteunen een levendige gemeenschap van verkopers en bieden u een uitgebreide keuze. Verken en vind wat u zoekt!</h3>
                </div>
            </div>
        </div>

        <div>

            <div class="grid xl:grid-cols-6 lg:grid-cols-4 md:grid-cols-3 grid-cols-2 gap-y-2 pb-32 min-h-[61.5vh]">


                @foreach ($imagesSrc as $imageSrc)
                    <a href="{{route('seller', ['seller' => str_replace('.png', '', $imageSrc)])}}"><img src="{{asset('imgs/sellers/'.$imageSrc)}}" alt=""></a>

                @endforeach

            </div>

        </div>            

    </div>

@endsection