@props(['compact' => false, 'color' => 'currentColor'])

<svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => 'inline-block']) }}>
    <defs>
        <linearGradient id="walletGrad{{ $compact ? 'C' : '' }}" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#6366f1"/>
            <stop offset="50%" stop-color="#8b5cf6"/>
            <stop offset="100%" stop-color="#a855f7"/>
        </linearGradient>
        <linearGradient id="walletHi{{ $compact ? 'C' : '' }}" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#ffffff" stop-opacity="0.25"/>
            <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
        </linearGradient>
    </defs>
    <rect x="3" y="9" width="34" height="24" rx="4" fill="url(#walletGrad{{ $compact ? 'C' : '' }})"/>
    <rect x="3" y="9" width="34" height="12" rx="4" fill="url(#walletHi{{ $compact ? 'C' : '' }})"/>
    <path d="M3 12a4 4 0 014-4h22l5 5H3v-1z" fill="url(#walletGrad{{ $compact ? 'C' : '' }})" opacity="0.95"/>
    <rect x="3" y="9" width="34" height="3" rx="1.5" fill="#ffffff" opacity="0.15"/>
    <circle cx="29" cy="21" r="4" fill="#ffffff" opacity="0.95"/>
    <circle cx="29" cy="21" r="2" fill="url(#walletGrad{{ $compact ? 'C' : '' }})"/>
    <text x="9" y="25" font-family="system-ui, sans-serif" font-size="9" font-weight="700" fill="#ffffff" opacity="0.9">$</text>
</svg>
