@props([
    'variant' => 'default',
    'size' => 'sm',
])

@php
$baseClasses = 'inline-flex items-center font-medium rounded-md tracking-tight transition-colors';

$sizeClasses = match ($size) {
    'xs' => 'text-[11px] px-2 py-0.5',
    'md' => 'text-sm px-3 py-1',
    default => 'text-xs px-2.5 py-0.5',
};

$variantClasses = match ($variant) {
    'magang' => 'border border-stone-200 bg-stone-100/70 text-stone-800 font-semibold',
    'paruh_waktu', 'persimmon' => 'border border-orange-200 bg-orange-50 text-orange-800 font-semibold',
    'onsite', 'remote', 'hybrid', 'sistem_kerja' => 'border border-stone-200 bg-white text-stone-700',
    'earning', 'emerald' => 'border border-emerald-200 bg-emerald-50 text-emerald-800 font-semibold',
    'obsidian', 'ink' => 'border border-slate-800 bg-slate-900 text-white font-medium',
    'slate' => 'border border-slate-200 bg-slate-100 text-slate-800 font-medium',
    default => 'border border-stone-200 bg-stone-100/70 text-stone-800',
};

$classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
