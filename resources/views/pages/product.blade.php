@extends('layouts.app')
@section('content')
<div class="px-5">
    <div class="xl:w-[1240px] mx-auto relative bg-white p-4 md:p-10 my-10 rounded-lg shadow-lg " x-data="{info: false}">
        <div class="md:flex">
            <div class="md:w-[20%] ">
                   
                <div id="default-carousel" class="relative md:mb-0 mb-10" data-carousel="static">
                    <div class="overflow-hidden relative h-[220px] min-w-[180px] w-full rounded-lg grid place-items-center ">
                        
                        @foreach ($product as $item)
 
                            <div class="hidden duration-700 ease-in-out h-[180px] min-w-[180px] " data-carousel-item>
                                <img src="https://www.kooponline.com/imageCache.php?src={{base64_encode($item->image_url)}}" class="block absolute top-1/2 left-1/2 w-full  -translate-x-1/2 -translate-y-1/2" alt="{{$item->merchant}}">
                            </div>

                        @endforeach

                    </div>

                    @if(count($product) > 1)

                        <div class="w-full relative flex">
                            <button class="w-1/2" data-carousel-prev><i class="fa-solid fa-arrow-left"></i></button>
                            <button class="w-1/2" data-carousel-next><i class="fa-solid fa-arrow-right"></i></button>
                        </div>

                    @endif

                </div>
            
                <script src="https://unpkg.com/flowbite@1.4.0/dist/flowbite.js"></script>

            </div>
            <div class="md:px-10 px-4 grid gap-y-3">
                <h1 class="text-xl font-semibold">{{$product[0]->name}}</h1>
                <p>{{$description}}</p>
                <div>
                    <h2><span class="font-bold">Prijs:</span> €{{$product[0]->price}}</h2>
                    <h2><span class="font-bold">EAN:</span> {{$product[0]->ean}}</h2>
                    <h2><span class="font-bold">Merk:</span> <a href="{{route('brand', ['brand' => $product[0]->brand])}}">{{$product[0]->brand}}</a></h2>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
            <table class="table-container overflow-x-auto mt-10 w-[1160px] relative" x-data="{bol: false}" style="overflow-x: auto;">

                <tr class="text-left w-[1160px] relative">
                    
                    <th class="p-5">Webwinkel</th>
                    <th>Product Naam</th>
                    <th class="w-[10rem]">Product Prijs</th>
                    <th class="text-right my-auto text-sm w-[10rem]"></th>
    
                </tr>
    
                {{-- WebStore Section --}}
    
                @if(array_key_exists('products', $bolData) && array_key_exists('offers', $bolData['products'][0]['offerData']))
    
                    <tr class="w-full p-3 even:bg-gray-100 " x-show="!bol" >
                        <td class="p-2 min-w-[170px]">
                            <img class="h-[56px] w-[170px]" src="{{asset('imgs/sellers/Bol.png')}}" alt="">
                        </td>
                        <td class="w-fit">
                            <span class="my-auto font-semibold px-2">{{$bolData['products'][0]['title']}}</span>
                        </td>
                        <td class="w-fit">
                            @foreach ( $bolData['products'][0]['offerData']['offers'] as $offer)
    
                                @if($offer['bestOffer']) 
                                    <span class="my-auto font-semibold">€ {{$offer['price']}}</span>
                                @endif
                            
                            @endforeach
    
                        </td>
                        <td class="pr-2 w-fit">

                            <a target="_blank" href="{{$bolData['products'][0]['urls'][0]['value']}}">
                                <button class="bg-accent2 rounded-lg float-right p-2 text-sm text-white font-semibold">
                                    Bezoek webwinkel
                                </button>
                            </a>
    
                        </td>
                    </tr>
    
                @endif
                    
    
                @foreach ($product as $item)
    
                    <tr class="w-full p-3 even:bg-gray-100 " x-show="!bol">
                        <td class="p-2">
                            <a href="{{route('seller', ['seller' => $item->merchant])}}">
                                <img class="h-[56px] w-[170px]" src="{{asset('imgs/sellers/'.$item->merchant.'.png')}}" alt="">
                            </a>
                        </td>
                        <td>
                            <span class="my-auto font-semibold px-2">{{$item->original_name}}</span>
                        </td>
                        <td>
                            <span class="my-auto font-semibold"> € {{$item->price}}</span>
                        </td>
                        <td class="pr-2">
    
                            <a target="_blank" href="{{$item->buy_url}}">
                                <button class="bg-accent2 rounded-lg float-right p-2 text-sm w-fit text-white font-semibold">
                                    Bezoek webwinkel
                                </button>
                            </a>
                            
                        </td>
                    </tr>
    
                @endforeach
    
            </table>
        </div>

        <h1 class="px-2 pt-10 text-sm text-gray-500 italic text-right cursor-pointer" @click="info = ! info">Bekijk Product Beschrijvingen <i x-show="!info" class="fa-solid fa-angle-down ml-2"></i> <i x-show="info" class="fa-solid fa-angle-up ml-2"></i></h1>

        <table class="mt-10 w-full " x-show="info"> 

            <tr class="text-left">
                
                <th class="p-5">Webwinkel</th>
                <th>Product Beschrijving</th>

            </tr>

            @if(array_key_exists('products', $bolData) && array_key_exists('offers', $bolData['products'][0]['offerData']))

                <tr class="w-full p-3 even:bg-gray-100 ">
                    <td class="p-2 w-[220px] h-[84px]">
                        <img class="h-[56px] w-[170px]" src="{{asset('imgs/sellers/Bol.png')}}" alt="">
                    </td>
                    <td class=" pr-2 py-2">
                        <span class="my-auto font-semibold">{{$bolData['products'][0]['shortDescription'] ?? 'None'}}</span>
                    </td>

                </tr>

            @endif

            @foreach ($product as $item)
                <tr class="w-full p-3 even:bg-gray-100 ">
                    <td class="p-2 w-[220px] h-[84px]">
                        <img class="h-[56px] w-[170px]" src="{{asset('imgs/sellers/'.$item->merchant.'.png')}}" alt="">
                    </td>
                    <td class=" pr-2 py-2">
                        <span class="my-auto font-semibold">{{$item->description}}</span>
                    </td>

                </tr>

            @endforeach

        </table>

                



            
    </div>

    <div class="xl:w-[1240px] mx-auto relative bg-white p-4 md:p-10 my-10 rounded-lg shadow-lg mt-5 " x-data="{info: false}">
        <div>
            <h1 class="font-bold text-xl  border-b-2 border-opacity-10 pb-2 border-black text-accent2"><span class="text-accent">Relevante</span> Producten</h1>
            <div class="w-full grid lg:grid-cols-4 sm:grid-cols-2 gap-x-10 mt-10">
            
                @foreach ($relevantProducts as $item)
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
            
        </div>
    </div>
</div>

@endsection
