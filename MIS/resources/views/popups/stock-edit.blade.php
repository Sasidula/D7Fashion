<div class="bg-white rounded-lg shadow-md p-6">

    <h2 class="text-2xl font-bold mb-6 text-[#2a3f7d]">Edit material details</h2>

    <form>

        <div class="w-80 flex justify-center flex-col pb-10">
            <label class="block text-gray-700 mb-1" for="fullName">Select material Name</label>
            <select name="" id=" "  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></select>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

            <div>
                <label class="block text-gray-700 mb-1" for="password">Supplier Name</label>
                <input id="password" type="password" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div>
                <label class="block text-gray-700 mb-1" for="phone">Buying Price</label>
                <input id="phone" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>


            <div>
                <label class="block text-gray-700 mb-1" for="email">description</label>
                <input id="email" type="email" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>




        </div>

        <button type="submit" class="flex items-center justify-center w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 rounded-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9l-6 6-6-6"/>
            </svg>
            Save
        </button>
    </form>
</div>
