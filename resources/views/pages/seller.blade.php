@extends('layouts.app')
@section('content')
<div class="xl:w-[1240px] px-5 mx-auto relative my-10 grid gap-y-5">
    <livewire:components.filter.seller :filterCategory="$filterCategory" :filterBrands="$filterBrands" :query="$search" :url="$url" />
        <div>
            <ul class="grid gap-y-5 mb-10">
                @foreach ($productResults as $result)
                    <li class="bg-white rounded-lg p-5 border-2 border-black border-opacity-10 max-h-[144px] overflow-hidden xl:block sm:flex">
                        <div class="md:flex float-left sm:w-3/4">
                            <img class="mt-5 h-[80px] w-[80px] float-left" src="https://www.kooponline.com/imageCache.php?src={{base64_encode($result->image_url)}}" alt=""> 
                            <div class="max-h-[100px] overflow-hidden pl-5 sm:block hidden">
                                <h2 class="font-semibold truncate">{{$result->name}}</h2>
                                <p class="overflow-ellipsis overflow-hidden">{{$result->description}}</p>
                            </div>

                        </div>
                        <h2 class="font-semibold truncate text-center sm:hidden blocks">{{$result->name}}</h2>
                        
                        <div class="h-fit sm:my-auto md:min-w-[150px] text-center sm:gap-x-10 gap-x-5 ml-auto items-center w-fit">
                            <h2 class="text-center text-sm text-gray-500 mt-2 mb-2"><span class="italic">Vanaf</span> €{{$result->price}}</h2>
                            <a href="{{route('product', ['product_name' => str_replace(' ', '-', $result->normalised_name)])}}">
                                <button class="bg-accent2 sm:mx-auto text-white hover:bg-opacity-70 font-semibold p-2 rounded min-w-[150px]">
                                    @if($result->count == 1) 
                                        Meer Informatie
                                    @else 
                                        Vergelijk {{$result->count}} Prijzen
                                    @endif
                                </button>
                            </a>
                        </div>
                    </li>
                    
                @endforeach
            </ul>
            {{ $productResults->links() }}
        </div>
    </div>
@endsection
