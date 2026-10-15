@props(['id', 'label', 'messages' => []])

<div>
    <div class="flex items-center justify-between">
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
        {{ $action ?? '' }}
    </div>

    <input id="{{ $id }}" {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-700 focus:ring-brand-700 sm:text-sm']) }}>

    @foreach ($messages as $message)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @endforeach
</div>
