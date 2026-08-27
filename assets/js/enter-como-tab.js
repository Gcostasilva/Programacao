document.addEventListener('keydown', function (e) {

const el = e.target;
const tag = el.tagName;

// Só interfere na tecla Enter
if (e.key !== 'Enter') return;

// Enter em botão mantém o comportamento nativo
const ehBotao =
    tag === 'BUTTON' ||
    el.getAttribute('role') === 'button' ||
    (tag === 'INPUT' && ['submit', 'button', 'reset'].includes(el.type));

if (ehBotao) return;

// Em textarea, mantém o Enter para quebra de linha
if (tag === 'TEXTAREA') return;

// Cancela o comportamento padrão do Enter
e.preventDefault();

moverFocoParaProximo(el);
});


function moverFocoParaProximo(atual) {

    const focaveis = Array.from(document.querySelectorAll(
        'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )).filter(function (el) {
        // Ignora elementos escondidos (ex: campos de um modal fechado)
        return el.offsetParent !== null;
    });

    const indiceAtual = focaveis.indexOf(atual);

    if (indiceAtual > -1 && indiceAtual < focaveis.length - 1) {
        focaveis[indiceAtual + 1].focus();
    }
}



(function () {
    const salvo = localStorage.getItem('temaPreset');
    if (!salvo) return;

    const tema = JSON.parse(salvo);
    const html = document.documentElement;

    html.style.setProperty('--bs-primary', tema.primary);

    // sidebar/navbar/footer ainda não existem no momento do <head>,
    // então aplicamos assim que o restante do HTML estiver pronto
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.app-sidebar');
        const navbar = document.querySelector('.app-header');
        const footer = document.querySelector('.app-footer');

        if (sidebar) {
            sidebar.style.background = tema.sidebar;
            sidebar.setAttribute('data-bs-theme', tema.texto === '#111827' ? 'light' : 'dark');
        }
        if (navbar) {
            navbar.style.background = tema.navbar;
            navbar.setAttribute('data-bs-theme', tema.texto === '#111827' ? 'light' : 'dark');
        }
        if (footer) {
            footer.style.background = tema.footer;
        }
    });
})();
