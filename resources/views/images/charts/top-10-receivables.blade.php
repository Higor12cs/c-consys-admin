<div class="w-full">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="bg-orange-800 text-white px-3 py-2 mb-1 flex items-center">
                <span class="text-sm font-bold uppercase">TOP 10 VENCIDOS</span>
                <span class="ml-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-trending-down-icon lucide-trending-down">
                        <path d="M16 17h6v-6" />
                        <path d="m22 17-8.5-8.5-5 5L2 7" />
                    </svg>
                </span>
            </div>
            <table class="w-full text-xs border-collapse table-fixed">
                <thead>
                    <tr class="bg-orange-800 text-white">
                        <th class="text-left py-1 px-2 font-bold uppercase">Cliente</th>
                        <th class="text-right py-1 px-2 font-bold w-20">R$</th>
                        <th class="text-right py-1 px-2 font-bold w-12">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->vencidos as $item)
                        @if ($loop->first)
                            <tr class="bg-orange-200 border-b border-orange-200">
                                <td class="py-1 px-2 text-gray-900 font-bold uppercase truncate"
                                    title="{{ $item->description }}">
                                    {{ $item->description }}
                                </td>
                                <td class="py-1 px-2 text-right font-semibold text-gray-900 w-20">
                                    {{ formatAbbreviatedNumber($item->actual) }}
                                </td>
                                <td class="py-1 px-2 text-right text-gray-800 w-12">
                                    {{ formatPercentage($item->target, 0) }}
                                </td>
                            </tr>
                        @else
                            <tr class="bg-orange-50 border-b border-orange-100">
                                <td class="py-1 px-2 text-gray-900 font-medium uppercase truncate"
                                    title="{{ $item->description }}">
                                    {{ $item->description }}
                                </td>
                                <td class="py-1 px-2 text-right font-semibold text-gray-900 w-20">
                                    {{ formatAbbreviatedNumber($item->actual) }}
                                </td>
                                <td class="py-1 px-2 text-right text-gray-800 w-12">
                                    {{ formatPercentage($item->target, 0) }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>
            <div class="bg-green-800 text-white px-3 py-2 mb-1 flex items-center">
                <span class="text-sm font-bold uppercase">TOP 10 NÃO VENCIDOS</span>
                <span class="ml-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-trending-up-icon lucide-trending-up">
                        <path d="M16 7h6v6" />
                        <path d="m22 7-8.5 8.5-5-5L2 17" />
                    </svg>
                </span>
            </div>
            <table class="w-full text-xs border-collapse table-fixed">
                <thead>
                    <tr class="bg-green-800 text-white">
                        <th class="text-left py-1 px-2 font-bold uppercase">Cliente</th>
                        <th class="text-right py-1 px-2 font-bold w-20">R$</th>
                        <th class="text-right py-1 px-2 font-bold w-12">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->a_vencer as $item)
                        @if ($loop->first)
                            <tr class="bg-green-200 border-b border-green-200">
                                <td class="py-1 px-2 text-gray-900 font-bold uppercase truncate"
                                    title="{{ $item->description }}">
                                    {{ $item->description }}
                                </td>
                                <td class="py-1 px-2 text-right font-semibold text-gray-900 w-20">
                                    {{ formatAbbreviatedNumber($item->actual) }}
                                </td>
                                <td class="py-1 px-2 text-right text-gray-800 w-12">
                                    {{ formatPercentage($item->target, 0) }}
                                </td>
                            </tr>
                        @else
                            <tr class="bg-green-50 border-b border-green-100">
                                <td class="py-1 px-2 text-gray-900 font-medium uppercase truncate"
                                    title="{{ $item->description }}">
                                    {{ $item->description }}
                                </td>
                                <td class="py-1 px-2 text-right font-semibold text-gray-900 w-20">
                                    {{ formatAbbreviatedNumber($item->actual) }}
                                </td>
                                <td class="py-1 px-2 text-right text-gray-800 w-12">
                                    {{ formatPercentage($item->target, 0) }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
