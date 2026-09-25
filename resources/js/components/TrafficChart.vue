<script setup>
import { computed } from "vue";
const props = defineProps({ days: { type: Array, default: () => [] } });
const maximum = computed(() => Math.max(1, ...props.days.map((d) => d.count)));
const points = computed(() =>
    props.days
        .map(
            (d, i) =>
                `${12 + i * (636 / Math.max(1, props.days.length - 1))},${132 - (d.count / maximum.value) * 112}`,
        )
        .join(" "),
);
const label = computed(() =>
    props.days.map((d) => `${d.day}: ${d.count} clicks`).join("; "),
);
</script>
<template>
    <div class="traffic-chart">
        <svg
            viewBox="0 0 660 152"
            preserveAspectRatio="none"
            role="img"
            :aria-label="label"
        >
            <path
                d="M12 20H648 M12 76H648 M12 132H648"
                stroke="#e7e7ee"
                stroke-dasharray="4 6"
                fill="none"
            />
            <polygon :points="`12,132 ${points} 648,132`" fill="#eceefa" />
            <polyline
                :points="points"
                stroke="#5964c9"
                stroke-width="3"
                stroke-linejoin="round"
                stroke-linecap="round"
                fill="none"
                vector-effect="non-scaling-stroke"
            />
            <circle
                v-for="(day, i) in days"
                :key="day.day"
                :cx="12 + i * (636 / Math.max(1, days.length - 1))"
                :cy="132 - (day.count / maximum) * 112"
                r="3"
                fill="#5964c9"
            >
                <title>{{ day.day }}: {{ day.count }} clicks</title>
            </circle>
        </svg>
        <div class="chart-axis">
            <span>{{ days[0]?.day }}</span
            ><span>Daily clicks · UTC</span><span>{{ days.at(-1)?.day }}</span>
        </div>
    </div>
</template>
