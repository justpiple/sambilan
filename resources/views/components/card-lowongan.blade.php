@props([
    'lowongan',
])

<x-job-card :lowongan="$lowongan" {{ $attributes }} />
