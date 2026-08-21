document.addEventListener('keydown', function (e) {

    if (e.key !== 'Enter') return;

    const el = e.target;
    const tag = el.tagName;

    // Enter em botão (ou algo com papel de botão) mantém o comportamento nativo: clica.
    const ehBotao =
        tag === 'BUTTON' ||
        el.getAttribute('role') === 'button' ||
        (tag === 'INPUT' && ['submit', 'button', 'reset'].includes(el.type));

    if (ehBotao) return;

    // Em textarea, mantém o Enter pra quebra de linha.
    if (tag === 'TEXTAREA') return;

    // Qualquer outro campo: cancela o "submit" automático e pula pro próximo campo focável.
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


