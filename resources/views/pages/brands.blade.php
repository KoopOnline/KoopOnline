@extends('layouts.app')
@section('content')

    <div class="xl:w-[1500px] mx-auto relative grid gap-y-10 xl:my-10 px-5">

        <div class=" my-auto rounded-lg flex relative mt-4">
            <div class="relative">
                <div class="w-full">
                    <h2 class="font-bold text-xl mb-2 border-b-2 border-black border-opacity-10 pb-2"><span class="text-accent">Producten per</span> <span class="text-accent2">Merk</span></h2>
                    <h3 class=" text-gray-500 tracking-wider">Ontdek op deze pagina alle merken met producten op KoopOnline.com. Gebruik onze handige zoekfunctie om snel uw favoriete merk te vinden. Klik op het gewenste merk en bekijk het uitgebreide assortiment aan producten dat beschikbaar is.</h3>
                </div>
            </div>
        </div>

        <livewire:components.brands-search />

        @foreach ($categories as $key => $value)
        
            <div>

                <h1 class="font-bold  w-full pb-1 mb-1 text-lg ">{{$key}}</h1>

                <div class="grid 2xl:grid-cols-4 lg:grid-cols-3 grid-cols-2 gap-y-2 text-gray-600">

                    @foreach ($value as $brand) 

                        <a href="{{route('brand', ['brand' => str_replace(' ', '-', $brand)])}}" class="cursor-pointer hover:text-accent2">{{$brand}}</a>   

                    @endforeach

                </div>

            </div>
                        
        @endforeach

    </div>

@endsection