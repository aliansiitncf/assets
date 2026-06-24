import "./bootstrap";
import Chart from "chart.js/auto";
window.Chart = Chart;

const DARK_THEME = "dim";
const LIGHT_THEME = "bumblebee";

function initThemeToggle() {
    const toggle = document.getElementById("theme-toggle");
    if (!toggle) return;

    const savedTheme = localStorage.getItem("theme") ?? LIGHT_THEME;

    document.documentElement.setAttribute("data-theme", savedTheme);

    toggle.checked = savedTheme === DARK_THEME;

    const fresh = toggle.cloneNode(true);
    toggle.replaceWith(fresh);

    fresh.addEventListener("change", () => {
        const theme = fresh.checked ? DARK_THEME : LIGHT_THEME;
        localStorage.setItem("theme", theme);
        document.documentElement.setAttribute("data-theme", theme);
    });
}

// ✅ Full page load
document.addEventListener("DOMContentLoaded", initThemeToggle);

// ✅ Setiap kali Livewire update DOM
document.addEventListener("livewire:initialized", initThemeToggle);
document.addEventListener("livewire:updated", initThemeToggle);
