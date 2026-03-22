<div class="bg-black text-white min-h-screen flex flex-col items-center container mx-auto flex max-w-6xl flex-col items-center justify-center pt-10">
    
    <div class="text-center max-w-4xl mx-auto mb-12"> 
        <h1 class="text-6xl md:text-8xl font-bold tracking-tight mb-6">
            Tools Made Easy.
        </h1>
        <p class="text-xl md:text-2xl text-gray-400 mb-10">
            Modern tools for everyone
        </p>
        
        <button class="bg-[#6333FF] hover:bg-[#5228D9] text-white px-8 py-3 rounded-lg font-semibold transition-all duration-300">
            Get Started Now!
        </button>
    </div>
    
    <div class="w-full py-10 mx-auto">
        <div class="relative bg-[#9966FF] rounded-[40px] w-full min-h-[650px] shadow-2xl overflow-hidden">
        
            <div class="absolute top-8 left-8 z-10 w-[280px] md:w-[500px] opacity-90">
                <div class="rounded-xl border border-white/30 bg-white/10 backdrop-blur-md shadow-lg overflow-hidden">

                    <!-- HEADER -->
                    <div class="px-5 py-4 text-sm font-bold text-black/75 uppercase tracking-wide bg-white text-center">
                        Tools
                    </div>

                    <!-- TABLE -->
                    <table class="w-full text-sm text-black">
                        <thead>
                            <tr class="text-left text-xs font-medium text-black bg-white/90">
                                <th class="px-5 py-3 w-10">No</th>
                                <th class="px-5 py-3">Name</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>

                        <tbody class="text-sm" id="toolsTableBody">
                            <!-- Rows will be dynamically updated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="absolute top-8 right-8 z-10 w-[200px] md:w-[450px] opacity-90">
                <img src="{{ asset('images/notification.svg') }}" alt="Notification" class="w-full h-auto">
            </div>

            <div class="absolute inset-0 flex items-end justify-center z-30 pointer-events-none">
                <div class="relative flex items-end justify-center w-full max-w-6xl gap-4"> 
                    <!-- Cat Image with animation -->
                    <img id="catImage" src="{{ asset('images/cat.svg') }}" alt="Cat" 
                         class="w-[25rem] md:w-[30rem] h-auto transform translate-y-4 z-40 transition-opacity duration-500">
                    
                    <!-- Dino Image with animation -->
                    <img id="dinoImage" src="{{ asset('images/dino.svg') }}" alt="Dino" 
                         class="w-[25rem] md:w-[30rem] h-auto transform translate-y-4 z-30 transition-opacity duration-500">
                </div>
            </div>

            <div class="absolute bottom-8 left-8 z-50">
                <div class="bg-white rounded-xl border-4 border-black px-6 py-4 flex items-center gap-10 shadow-lg min-w-[300px] md:min-w-[400px]">
                    <span class="font-bold text-gray-800 whitespace-nowrap">67 Hours Remaining</span>
                    <span class="text-gray-800 font-medium">0%</span>
                    <span class="font-bold text-gray-800 whitespace-nowrap">Rent Time</span>
                </div>
            </div>

   
            <div class="absolute bottom-8 right-8 z-50 flex flex-col items-end" id="svgStack">
               
            </div>
        </div>
    </div>
    <div class="w-full max-w-6xl mx-auto py-24 px-4">
    <div class="relative border border-white/30 rounded-[30px] p-10 md:p-16 flex flex-col md:flex-row items-center justify-between overflow-visible">
        
        <div class="max-w-xl">
            <h2 class="text-5xl font-bold mb-6">
                <span class="relative inline-block">
                    Join Us!
                    
                </span>
            </h2>
            <p class="text-gray-400 text-lg leading-relaxed">
                Web peminjaman yang paling sigma di antara yang tersigma lainya , dengan mewing yang sangat ohio , dapatkan penawaran sekarang!
            </p>
        </div>

        <div class="relative w-[200px] md:w-[367px] h-[120px] md:h-[150px]">
            <img 
                src="{{ asset('images/sit-cat.png') }}" 
                alt="Sitting Cat"
                class="absolute bottom-0 right-0 w-full h-auto translate-y-63"
            >
        </div>

    </div>
</div>  
</div>
    @include('sections.hero.hero-style')
@include('sections.hero.hero-script')