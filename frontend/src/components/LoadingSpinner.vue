<template>
    <div class="loading-spinner" :class="{ 'loading-spinner--fullpage': fullpage }">
        <div class="loading-spinner__content">
            <div class="loading-spinner__icon">
                <div class="loading-spinner__snake">
                    <div
                        class="icon"
                        style="mask-image: url('/assets/icons/snake.svg')"
                    ></div>
                </div>
                <svg class="loading-spinner__circle" viewBox="0 0 50 50">
                    <circle
                        class="path"
                        cx="25"
                        cy="25"
                        r="20"
                        fill="none"
                        stroke-width="3"
                    ></circle>
                </svg>
            </div>
            <div v-if="text" class="loading-spinner__text">{{ text }}</div>
        </div>
    </div>
</template>

<script>
export default {
    name: "LoadingSpinner",
    props: {
        text: {
            type: String,
            default: "Загрузка...",
        },
        fullpage: {
            type: Boolean,
            default: false,
        },
    },
};
</script>

<style scoped>
.loading-spinner {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    min-height: 200px;
    height: 100%;
}

.loading-spinner--fullpage {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(4px);
    z-index: 9999;
    min-height: 100vh;
}

.loading-spinner__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}

.loading-spinner__icon {
    position: relative;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-spinner__snake {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    animation: pulse 2s ease-in-out infinite;
}

.loading-spinner__snake .icon {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #00ff88 0%, #00cc6a 100%);
    mask-size: contain;
    mask-repeat: no-repeat;
    mask-position: center;
}

.loading-spinner__circle {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.loading-spinner__circle .path {
    stroke: linear-gradient(135deg, #00ff88 0%, #00cc6a 100%);
    stroke: #00ff88;
    stroke-linecap: round;
    animation: dash 1.5s ease-in-out infinite;
}

.loading-spinner__text {
    font-size: 14px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.8);
    text-align: center;
    animation: fadeInOut 2s ease-in-out infinite;
}

/* Анимации */
@keyframes dash {
    0% {
        stroke-dasharray: 1, 150;
        stroke-dashoffset: 0;
    }
    50% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -35;
    }
    100% {
        stroke-dasharray: 90, 150;
        stroke-dashoffset: -124;
    }
}

@keyframes pulse {
    0%,
    100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 0.8;
    }
}

@keyframes fadeInOut {
    0%,
    100% {
        opacity: 0.6;
    }
    50% {
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .loading-spinner {
        min-height: 150px;
        padding: 30px 15px;
    }

    .loading-spinner__icon {
        width: 60px;
        height: 60px;
    }

    .loading-spinner__snake {
        width: 30px;
        height: 30px;
    }

    .loading-spinner__text {
        font-size: 12px;
    }
}
</style>

