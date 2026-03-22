@push('styles')
<style>
    /* Container stack */
    #svgStack {
        position: absolute;
        bottom: 2rem;
        right: 2rem;
        z-index: 50;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        pointer-events: none;
        width: auto;
        height: auto;
    }
    
    /* Style untuk setiap item */
    .stack-item {
        position: absolute;
        bottom: 0;
        right: 0;
        transition: all 0.9s cubic-bezier(0.25, 0.1, 0.15, 1.05);
        will-change: transform, opacity;
        transform-origin: right center;
        box-shadow: 0 10px 25px -8px rgba(0, 0, 0, 0.3);
        border-radius: 8px;
        overflow: hidden;
        background: white;
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
    }
    
    /* Variasi lebar untuk setiap posisi */
    .stack-item[data-id="1"] {
        width: 200px;
    }
    .stack-item[data-id="2"] {
        width: 180px;
    }
    .stack-item[data-id="3"] {
        width: 160px;
    }
    .stack-item[data-id="4"] {
        width: 160px;
    }
    
    @media (min-width: 768px) {
        .stack-item[data-id="1"] {
            width: 22rem;
        }
        .stack-item[data-id="2"] {
            width: 23rem;
        }
        .stack-item[data-id="3"] {
            width: 22rem;
        }
        .stack-item[data-id="4"] {
            width: 22rem;
        }
    }
    
    /* Gambar dalam item */
    .stack-item img {
        display: block;
        width: 100%;
        height: auto;
        transition: inherit;
    }
    
    /* Hilangkan default margin/padding */
    .tools-row {
        transition: all 0.3s ease;
    }

    .exiting-row {
        background-color: #fef2f2;
        border-left: 4px solid #ef4444;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1);
    }

    .entering-row {
        background-color: #f0fdf4 !important;
        border-left: 4px solid #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        z-index: 10;
    }

    .status-change {
        animation: statusPulse 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: inline-block;
    }

    @keyframes statusPulse {
        0% { transform: scale(1); }
        50% { 
            transform: scale(1.2); 
            filter: brightness(1.3); 
        }
        100% { transform: scale(1); }
    }

    /* Animasi swipe down untuk row baru */
    @keyframes swipeDown {
        0% {
            transform: translateY(-100%);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .entering-row {
        animation: swipeDown 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    /* Efek highlight untuk row yang berubah */
    .highlight-new {
        background: linear-gradient(90deg, rgba(99, 51, 255, 0.1) 0%, rgba(99, 51, 255, 0) 100%);
    }
    
    .status-available {
        color: #065f46;
        font-weight: 500;
    }
    
    .status-borrowed {
        color: #991b1b;
        font-weight: 500;
    }

    /* Animasi fade untuk cat dan dino */
    #catImage, #dinoImage {
        transition: opacity 0.5s ease-in-out;
    }

    .fade-out {
        opacity: 0;
    }

    .fade-in {
        opacity: 1;
    }



    /* Idling animation untuk cat dan dino - rotasi lambat dengan anchor di tengah bawah */
#catImage {
    animation: catIdle 6s ease-in-out infinite;
    transform-origin: center bottom;
    will-change: transform;
}

#dinoImage {
    animation: dinoIdle 7s ease-in-out infinite;
    transform-origin: center bottom;
    will-change: transform;
}

/* Animasi idle untuk cat - lebih lambat dan lembut */
@keyframes catIdle {
    0%, 100% {
        transform: rotate(0deg) translateY(0);
    }
    25% {
        transform: rotate(1.5deg) translateY(-1px);
    }
    50% {
        transform: rotate(0deg) translateY(-2px);
    }
    75% {
        transform: rotate(-1.5deg) translateY(-1px);
    }
}

/* Animasi idle untuk dino - sedikit berbeda biar terlihat natural */
@keyframes dinoIdle {
    0%, 100% {
        transform: rotate(0deg) translateY(0);
    }
    20% {
        transform: rotate(1.2deg) translateY(-1px);
    }
    40% {
        transform: rotate(0.5deg) translateY(-2px);
    }
    60% {
        transform: rotate(-1.2deg) translateY(-1px);
    }
    80% {
        transform: rotate(-0.5deg) translateY(-1px);
    }
}

/* Efek tambahan saat berganti pose (tetap mempertahankan idle animation) */

/* Animasi untuk milestone */
.bg-white.rounded-xl {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    z-index: 1;
    background-color: #ffffff !important; /* Tambahkan background putih solid */
    border: 2px solid #000000; /* Tambahkan border hitam */
}

/* Progress bar di background */
.milestone-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 100%;
    background: #9966FF;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0.15;
    z-index: 1;
    pointer-events: none; /* Agar tidak mengganggu klik */
}

/* Pastikan teks di atas progress bar */
.bg-white.rounded-xl span {
    position: relative;
    z-index: 2;
}

/* Efek shine untuk 100% (di container) */
.bg-white.rounded-xl.shine-effect {
    position: relative;
    background-color: #ffffff !important; /* Pastikan background tetap putih */
}

.bg-white.rounded-xl.shine-effect::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    transform: rotate(30deg);
    z-index: 3;
    pointer-events: none;
}

@keyframes shine {
    0% {
        transform: translateX(-100%) rotate(30deg);
    }
    100% {
        transform: translateX(100%) rotate(30deg);
    }
}

/* Animasi angka pop */
@keyframes numberPop {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
        color: #ffffff;
    }
    100% {
        transform: scale(1);
    }
}

.number-pop {
    animation: numberPop 0.4s ease;
    display: inline-block;
}
</style>
@endpush