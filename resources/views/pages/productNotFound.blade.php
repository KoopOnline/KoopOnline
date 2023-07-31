@extends('layouts.app')
@section('content')
    <div class="w-[1500px] mx-auto relative py-10">
        <div class="px-40 py-20 bg-white rounded-md shadow-xl">
            <div class="flex flex-col items-center">
              <h1 class="font-bold text-accent2 text-9xl">404</h1>
        
              <h6 class="mb-2 text-2xl font-bold text-center text-gray-800 md:text-3xl">
                <span class="text-accent">Oops!</span> Pagina niet gevonden
              </h6>
        
              <p class="mb-8 text-center text-gray-500 md:text-lg">
                De pagina waar u naar zoekt bestaat niet.
              </p>
        
              <a
                href="{{route('home')}}"
                class="px-6 py-2 text-sm font-semibold text-blue-800 bg-blue-100"
                >Terug <i class="fa-solid fa-arrow-right"></i></a
              >
            </div>
          </div>
        
    </div>
@endsection