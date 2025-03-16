const createAnimation = (classes) => ({
    enter: (element) => {
        if (!element) return;
        
        // Adiciona classes iniciais
        element.classList.add('transition', 'duration-300', 'ease-out');
        element.classList.add(...classes.from.split(' '));

        // Aplica a transição no próximo frame
        requestAnimationFrame(() => {
            element.classList.remove(...classes.from.split(' '));
            element.classList.add(...classes.to.split(' '));
        });
    },
    leave: (element) => {
        if (!element) return;

        // Adiciona classes de saída
        element.classList.add('transition', 'duration-200', 'ease-in');
        element.classList.remove(...classes.to.split(' '));
        element.classList.add(...classes.from.split(' '));
    }
});

export const fadeInUp = createAnimation({
    from: 'transform opacity-0 translate-y-4',
    to: 'transform opacity-100 translate-y-0'
});

export const fadeIn = createAnimation({
    from: 'opacity-0',
    to: 'opacity-100'
});

export const slideIn = createAnimation({
    from: 'transform -translate-x-full',
    to: 'transform translate-x-0'
});
