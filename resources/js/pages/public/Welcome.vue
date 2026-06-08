<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    Bot,
    Boxes,
    BrainCircuit,
    CalendarCheck,
    ChartLine,
    ChartPie,
    CheckCircle2,
    Cloud,
    Code2,
    Clock,
    Dumbbell,
    ExternalLink,
    Facebook,
    Images,
    Instagram,
    Layers,
    Mail,
    Menu,
    MessageCircle,
    MessageSquareText,
    MonitorSmartphone,
    PawPrint,
    Pill,
    ReceiptText,
    Rocket,
    ServerCog,
    ShieldCheck,
    ShoppingCart,
    Smartphone,
    Store,
    Utensils,
    Wrench,
    X,
    Zap,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import type { Component } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import { store as loginStore } from '@/routes/login';
import { request as passwordRequest } from '@/routes/password';

type BusinessCategory = 'restaurant' | 'retail' | 'pharmacy' | 'hardware' | 'vet' | 'sport';
type PortfolioFilter = 'all' | BusinessCategory;

// Estado reactivo de la pagina.
// ref() es una variable que Vue puede observar y usar para actualizar la vista.
const isScrolled = ref(false);
const mobileMenuOpen = ref(false);
const loginModalOpen = ref(false);
const selectedFilter = ref<PortfolioFilter>('all');
const authUser = computed(() => usePage().props.auth?.user ?? null);

const scrollProgress = ref(0);
const pointerX = ref(0);
const pointerY = ref(0);
let revealObserver: IntersectionObserver | null = null;

// useForm es de Inertia. Sirve para enviar datos a Laravel sin recargar la pagina.
const form = useForm({
    name: '',
    email: '',
    phone: '',
    business_type: '',
    message: '',
});

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

const whatsappUrl =
    'https://wa.me/59177149775?text=Hola%20Soluci%C3%B3n%20Digital%2C%20quiero%20informaci%C3%B3n%20sobre%20sus%20sistemas%20SaaS%20y%20chatbot.';
const seoTitle = 'Solución Digital | Sistemas SaaS, chatbot y software a medida';
const seoDescription =
    'Solución Digital crea y alquila sistemas SaaS, punto de venta, inventario, facturación, chatbot, automatizaciones y software a medida para negocios.';

// Links del menu superior de la landing.
const navLinks = [
    { label: 'Inicio', href: '#inicio' },
    { label: 'Soluciones', href: '#soluciones' },
    { label: 'SaaS', href: '#saas' },
    { label: 'Casos de Éxito', href: '#portafolio' },
    { label: 'Beneficios', href: '#beneficios' },
    { label: 'Planes', href: '#planes' },
];

// Tarjetas de "Soluciones". Si quieres agregar otro tipo de negocio, empieza aqui.
const solutions: Array<{
    key: string;
    title: string;
    description: string;
    icon: Component;
    buttonClass: string;
}> = [
    {
        key: 'restaurante',
        title: 'Restaurantes',
        description:
            'Sistema integral con gestión de mesas, pedidos, cocina, inventario y reportes para optimizar tu restaurante.',
        icon: Utensils,
        buttonClass: 'btn-restaurant',
    },
    {
        key: 'comercio',
        title: 'Comercios',
        description:
            'Punto de venta completo con control de inventario, facturación electrónica y reportes de ventas.',
        icon: ShoppingCart,
        buttonClass: 'btn-retail',
    },
    {
        key: 'farmacia',
        title: 'Farmacias',
        description:
            'Sistema especializado con control de caducidades, recetas médicas y manejo de proveedores farmacéuticos.',
        icon: Pill,
        buttonClass: 'btn-pharmacy',
    },
    {
        key: 'ferreteria',
        title: 'Ferreterías',
        description:
            'Software para gestión de múltiples almacenes, variedad de medidas y conversiones de productos.',
        icon: Wrench,
        buttonClass: 'btn-hardware',
    },
    {
        key: 'veterinaria',
        title: 'Veterinarias',
        description:
            'Historial clínico de mascotas, control de vacunas, citas, hospitalización y recordatorios automáticos.',
        icon: PawPrint,
        buttonClass: 'btn-vet',
    },
    {
        key: 'saas',
        title: 'Distribuidor SaaS',
        description:
            'Alquila sistemas por rubro, administra clientes, usuarios, pagos, módulos y soporte desde una base escalable.',
        icon: Cloud,
        buttonClass: 'btn-saas',
    },
    {
        key: 'chatbot',
        title: 'Chatbot y Automatización',
        description:
            'Bots para WhatsApp y web que responden consultas, capturan leads, agendan citas y conectan con tu sistema.',
        icon: Bot,
        buttonClass: 'btn-chatbot',
    },
];

// Beneficios que se muestran en la seccion "Por que elegirnos".
const features = [
    {
        title: 'Aumento de Ventas',
        description:
            'Identifica oportunidades de venta cruzada, gestiona promociones y analiza el comportamiento de tus clientes para aumentar tus ingresos.',
        icon: ChartLine,
    },
    {
        title: 'Ahorro de Tiempo',
        description:
            'Automatiza procesos repetitivos como facturación, control de inventario y reportes, para enfocarte en hacer crecer tu negocio.',
        icon: Clock,
    },
    {
        title: 'Control de Inventario',
        description:
            'Mantén un control preciso de tu stock, recibe alertas de productos por agotarse y evita pérdidas por caducidad u obsolescencia.',
        icon: Boxes,
    },
    {
        title: 'Acceso en cualquier lugar',
        description:
            'Consulta tu negocio desde cualquier dispositivo con internet, manteniendo la seguridad y confidencialidad de tus datos.',
        icon: MonitorSmartphone,
    },
];

const heroStats = [
    { value: 'SaaS', label: 'alquiler mensual' },
    { value: 'Bot', label: 'ventas y soporte' },
    { value: '24/7', label: 'negocio conectado' },
];

const techBadges = [
    { label: 'IA para respuestas', icon: BrainCircuit },
    { label: 'Web y móvil', icon: Smartphone },
    { label: 'Desarrollo a medida', icon: Code2 },
];

const workflowSteps = [
    {
        title: 'Demo guiada',
        description: 'Ves el flujo real de tu negocio antes de decidir.',
        icon: MonitorSmartphone,
    },
    {
        title: 'Sistema listo',
        description: 'Configuramos usuarios, productos, permisos, reportes y bot.',
        icon: ServerCog,
    },
    {
        title: 'Soporte continuo',
        description: 'Tu equipo recibe acompanamiento y mejoras sin frenar ventas.',
        icon: MessageSquareText,
    },
];

const impactStats = [
    { value: '15 min', label: 'diagnostico inicial' },
    { value: '+5 rubros', label: 'plantillas listas' },
    { value: '1 solo panel', label: 'ventas, stock y clientes' },
    { value: 'WhatsApp', label: 'contacto directo' },
];

const servicePillars = [
    {
        eyebrow: 'Alquiler de sistema',
        title: 'Tu software funcionando sin comprar servidores',
        description:
            'Modelo SaaS para pagar por uso, activar módulos por cliente y crecer sin hacer una inversión pesada desde el primer día.',
        icon: Cloud,
        points: ['Usuarios y permisos', 'Backups y seguridad', 'Actualizaciones incluidas'],
    },
    {
        eyebrow: 'Distribuidor SaaS',
        title: 'Convierte el sistema en una línea de negocio',
        description:
            'Ideal para distribuidores que quieren vender soluciones por rubro con control de clientes, planes, soporte y renovaciones.',
        icon: Layers,
        points: ['Planes por rubro', 'Gestión de clientes', 'Ingresos recurrentes'],
    },
    {
        eyebrow: 'Chatbot inteligente',
        title: 'Atiende clientes incluso cuando estás ocupado',
        description:
            'Automatiza respuestas, cotizaciones, reservas, recordatorios y consultas frecuentes desde WhatsApp o tu sitio web.',
        icon: Bot,
        points: ['Leads automáticos', 'Agenda y recordatorios', 'Integración con sistema'],
    },
];

const liveModules = [
    { label: 'Punto de venta', value: 'Online', icon: ShoppingCart },
    { label: 'Inventario', value: '1.248 items', icon: Boxes },
    { label: 'Facturación', value: 'Lista', icon: ReceiptText },
    { label: 'Chatbot', value: '32 leads', icon: Bot },
    { label: 'Agenda', value: '18 citas', icon: CalendarCheck },
    { label: 'Reportes', value: '+27%', icon: ChartLine },
];

const plans = [
    {
        name: 'Emprende',
        price: 'Desde Bs 199',
        description: 'Para negocios que necesitan vender, controlar stock y ordenar su día.',
        features: ['Punto de venta', 'Inventario', 'Usuarios básicos', 'Soporte remoto'],
    },
    {
        name: 'Crecimiento',
        price: 'Desde Bs 349',
        description: 'Para empresas que quieren reportes, automatizaciones y más control.',
        features: ['Todo Emprende', 'Reportes gerenciales', 'Facturación', 'Chatbot inicial'],
        highlighted: true,
    },
    {
        name: 'Distribuidor',
        price: 'A medida',
        description: 'Para vender sistemas SaaS a varios clientes con una operación escalable.',
        features: ['Multi cliente', 'Planes y renovaciones', 'Marca adaptable', 'Soporte prioritario'],
    },
];

// Botones para filtrar los casos de exito.
const filters: Array<{ label: string; value: PortfolioFilter; icon?: Component }> = [
    { label: 'Todos', value: 'all' },
    { label: 'Restaurantes', value: 'restaurant', icon: Utensils },
    { label: 'Comercios', value: 'retail', icon: Store },
    { label: 'Farmacias', value: 'pharmacy', icon: Pill },
    { label: 'Ferreterías', value: 'hardware', icon: Wrench },
    { label: 'Veterinarias', value: 'vet', icon: PawPrint },
    { label: 'Deportes', value: 'sport', icon: Dumbbell },
];

// Casos de exito que aparecen en el portafolio.
const portfolioItems: Array<{
    category: BusinessCategory;
    categoryLabel: string;
    badgeClass: string;
    title: string;
    description: string;
    image: string;
    alt: string;
    link?: string;
}> = [
    {
        category: 'restaurant',
        categoryLabel: 'Restaurante',
        badgeClass: 'restaurant-badge',
        title: 'Restaurante "La Tradición"',
        description:
            'Sistema completo con módulos de mesas, cocina, delivery y reportes que aumentó su eficiencia en un 40%.',
        image: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Interior de restaurante moderno',
    },
    {
        category: 'retail',
        categoryLabel: 'Comercio',
        badgeClass: 'retail-badge',
        title: 'Tienda "El Buen Precio"',
        description:
            'Punto de venta con control de inventario que redujo las pérdidas por caducidad en un 65%.',
        image: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Tienda con carrito de compras',
    },
    {
        category: 'pharmacy',
        categoryLabel: 'Farmacia',
        badgeClass: 'pharmacy-badge',
        title: 'Farmacia "Salud Total"',
        description:
            'Sistema especializado que automatizó el control de caducidades y manejo de recetas médicas.',
        image: 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Medicamentos en estantería de farmacia',
    },
    {
        category: 'hardware',
        categoryLabel: 'Ferretería',
        badgeClass: 'hardware-badge',
        title: 'Ferretería "El Constructor"',
        description:
            'Sistema multi-almacén con conversión de medidas que mejoró la precisión del inventario.',
        image: 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Personas revisando productos de ferretería',
    },
    {
        category: 'vet',
        categoryLabel: 'Veterinaria',
        badgeClass: 'vet-badge',
        title: 'Clínica "Huellitas"',
        description:
            'Gestión integral de pacientes, control de vacunas y agenda de citas automatizada.',
        image: 'https://images.unsplash.com/photo-1623366302587-bce731d08e83?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
        alt: 'Atención veterinaria profesional',
    },
    {
        category: 'retail',
        categoryLabel: 'Comercio',
        badgeClass: 'retail-badge',
        title: 'Capresi',
        description:
            'Sistema de gestión completo para administrar ventas, inventario y clientes en tiempo real desde cualquier dispositivo.',
        image: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80',
        alt: 'Sistema de gestión comercial Capresi',
        link: 'https://capresi.soluciondigital.dev/',
    },
    {
        category: 'sport',
        categoryLabel: 'Deportes',
        badgeClass: 'sport-badge',
        title: 'Kaiteki Karate',
        description:
            'Plataforma de gestión para academia de artes marciales: control de alumnos, pagos, asistencia y progresión de cinturones.',
        image: 'https://images.unsplash.com/photo-1555597673-b21d5c935865?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80',
        alt: 'Academia de karate Kaiteki',
        link: 'https://kaiteki.soluciondigital.dev/admin/login',
    },
    {
        category: 'retail',
        categoryLabel: 'Remates',
        badgeClass: 'retail-badge',
        title: 'Remate Loma de Ovando',
        description:
            'Sistema para gestión de remates rurales: registro de lotes, compradores, pujas y liquidaciones automatizadas.',
        image: 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80',
        alt: 'Remate rural Loma de Ovando',
        link: 'https://elcorral.soluciondigital.dev/admin',
    },
    {
        category: 'vet',
        categoryLabel: 'Veterinaria',
        badgeClass: 'vet-badge',
        title: 'Consultorio Veterinario Cortez',
        description:
            'Sitio web y sistema de gestión para consultorio veterinario: agenda online, historial de pacientes y control de tratamientos.',
        image: 'https://images.unsplash.com/photo-1581888227599-779811939961?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80',
        alt: 'Consultorio Veterinario Cortez',
        link: 'https://consultorioveterinariocortez.com/',
    },
];

// Testimonios de clientes. Son datos estaticos por ahora.
const testimonials = [
    {
        quote:
            'El sistema para nuestro restaurante ha sido revolucionario. Ahora podemos atender más mesas con menos personal y nuestros clientes notan la diferencia en el servicio.',
        name: 'Carlos Mendoza',
        role: 'Dueño, Restaurante "La Parrilla"',
        image: 'https://randomuser.me/api/portraits/men/32.jpg',
    },
    {
        quote:
            'Desde que implementamos el sistema en nuestra farmacia, hemos reducido las pérdidas por caducidad en un 70% y el tiempo para hacer inventario se redujo de 2 días a 2 horas.',
        name: 'María Fernández',
        role: 'Gerente, Farmacia "Bienestar"',
        image: 'https://randomuser.me/api/portraits/women/44.jpg',
    },
    {
        quote:
            'El punto de venta transformó nuestro negocio. Ahora sabemos qué productos se venden más, en qué horarios y podemos tomar decisiones basadas en datos.',
        name: 'Roberto Jiménez',
        role: 'Dueño, Minimercado "El Ahorro"',
        image: 'https://randomuser.me/api/portraits/men/75.jpg',
    },
];

// computed() recalcula automaticamente cuando selectedFilter cambia.
const filteredPortfolioItems = computed(() =>
    selectedFilter.value === 'all'
        ? portfolioItems
        : portfolioItems.filter((item) => item.category === selectedFilter.value),
);

// Cambia el estilo del header cuando el usuario baja la pagina.
function handleScroll() {
    const scrollTop = window.scrollY;
    const scrollable = document.documentElement.scrollHeight - window.innerHeight;

    isScrolled.value = scrollTop > 50;
    scrollProgress.value = scrollable > 0 ? Math.min((scrollTop / scrollable) * 100, 100) : 0;
}

function handlePointerMove(event: MouseEvent) {
    pointerX.value = (event.clientX / window.innerWidth - 0.5) * 2;
    pointerY.value = (event.clientY / window.innerHeight - 0.5) * 2;
}

// Navegacion suave a una seccion de la misma pagina.
function scrollToSection(hash: string) {
    const section = document.querySelector(hash);

    if (!section) {
        return;
    }

    mobileMenuOpen.value = false;
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function openLoginModal() {
    mobileMenuOpen.value = false;
    loginModalOpen.value = true;
}

function closeLoginModal() {
    loginModalOpen.value = false;
    loginForm.clearErrors();
    loginForm.reset('password');
}

// Envia el formulario al controlador Laravel: ContactController@store.
function submit() {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function submitLogin() {
    loginForm.post(loginStore.url(), {
        preserveScroll: true,
        onSuccess: () => {
            loginModalOpen.value = false;
            loginForm.reset();
        },
        onError: () => loginForm.reset('password'),
    });
}

// onMounted() se ejecuta cuando Vue ya puso el componente en pantalla.
onMounted(() => {
    handleScroll();
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('pointermove', handlePointerMove, { passive: true });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.16 },
    );

    revealObserver = observer;

    document.querySelectorAll('.reveal').forEach((element) => {
        observer.observe(element);
    });
});

// Limpia el evento de scroll si Vue desmonta esta pagina.
onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('pointermove', handlePointerMove);
    revealObserver?.disconnect();
});
</script>

<template>
    <Head :title="seoTitle">
        <meta name="description" :content="seoDescription" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Poppins:wght@500;600;700;800&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div class="landing-page">
        <header :class="{ scrolled: isScrolled }">
            <div class="scroll-progress" :style="{ width: `${scrollProgress}%` }" aria-hidden="true"></div>
            <div class="container">
                <nav aria-label="Navegación principal">
                    <button type="button" class="logo-button" @click="scrollToSection('#inicio')">
                        <img
                            src="/image/logo.png"
                            alt="Solución Digital"
                            class="brand-logo"
                            width="1254"
                            height="1254"
                        />
                    </button>

                    <ul class="nav-links">
                        <li v-for="link in navLinks" :key="link.href">
                            <button type="button" @click="scrollToSection(link.href)">
                                {{ link.label }}
                            </button>
                        </li>
                    </ul>

                    <div class="nav-actions">
                        <button type="button" class="btn btn-sm" @click="scrollToSection('#contacto')">
                            Contacto
                        </button>
                        <Link v-if="authUser" href="/admin/dashboard" class="btn btn-sm btn-panel">
                            Ir al panel
                        </Link>
                        <button v-else type="button" class="login-link login-cta" @click="openLoginModal">
                            Iniciar sesión
                        </button>
                    </div>

                    <div class="mobile-actions">
                        <Link v-if="authUser" href="/admin/dashboard" class="btn btn-sm btn-panel">
                            Ir al panel
                        </Link>
                        <button v-else type="button" class="login-link login-cta" @click="openLoginModal">
                            Iniciar sesión
                        </button>
                        <button
                            type="button"
                            class="mobile-menu-button"
                            :aria-label="mobileMenuOpen ? 'Cerrar menú' : 'Abrir menú'"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                        >
                            <X v-if="mobileMenuOpen" class="icon" />
                            <Menu v-else class="icon" />
                        </button>
                    </div>
                </nav>

                <div v-if="mobileMenuOpen" class="mobile-menu">
                    <button
                        v-for="link in navLinks"
                        :key="link.href"
                        type="button"
                        @click="scrollToSection(link.href)"
                    >
                        {{ link.label }}
                    </button>
                    <button type="button" class="btn btn-sm" @click="scrollToSection('#contacto')">
                        Contacto
                    </button>
                    <Link v-if="authUser" href="/admin/dashboard" class="btn btn-sm btn-panel mobile-login">
                        Ir al panel
                    </Link>
                    <button v-else type="button" class="login-link login-cta mobile-login" @click="openLoginModal">
                        Iniciar sesión
                    </button>
                </div>
            </div>
        </header>

        <main>
            <section id="inicio" class="hero">
                <div class="hero-pixels" aria-hidden="true">
                    <i class="px px-c1"></i><i class="px px-c2"></i><i class="px px-c3"></i>
                    <i class="px px-c4"></i><i class="px px-c5"></i><i class="px px-c6"></i>
                    <i class="px px-c7"></i><i class="px px-c8"></i>
                    <i class="px px-p1"></i><i class="px px-p2"></i><i class="px px-p3"></i>
                    <i class="px px-p4"></i><i class="px px-p5"></i><i class="px px-p6"></i>
                    <i class="px px-p7"></i><i class="px px-p8"></i>
                </div>
                <div class="hero-container">
                    <div class="hero-text reveal">
                        <div class="hero-badge">
                            <Rocket class="badge-icon" />
                            Solución Digital | Sistemas SaaS + Chatbot
                        </div>
                        <h1>
                            Alquila software que vende,
                            <br />
                            controla y atiende por ti
                        </h1>
                        <p>
                            Creamos y alquilamos sistemas para negocios: punto de venta,
                            inventario, facturación, reportes, distribuidores SaaS, chatbot para
                            WhatsApp y automatizaciones conectadas a tu operación.
                        </p>
                        <div class="hero-tech-row" aria-label="Capacidades principales">
                            <span v-for="badge in techBadges" :key="badge.label" class="hero-tech-badge">
                                <component :is="badge.icon" class="hero-tech-icon" />
                                {{ badge.label }}
                            </span>
                        </div>
                        <div class="hero-btns">
                            <button type="button" class="btn" @click="scrollToSection('#contacto')">
                                Quiero una demo
                                <Rocket class="btn-icon" />
                            </button>
                            <button type="button" class="btn-outline" @click="scrollToSection('#saas')">
                                Ver SaaS y bot
                                <ArrowRight class="btn-icon" />
                            </button>
                        </div>
                        <div class="hero-stats" aria-label="Resultados principales">
                            <div v-for="stat in heroStats" :key="stat.label" class="hero-stat">
                                <strong>{{ stat.value }}</strong>
                                <span>{{ stat.label }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="hero-visual reveal delay-1"
                        :style="{
                            '--pointer-x': pointerX,
                            '--pointer-y': pointerY,
                        }"
                    >
                        <div class="visual-card">
                            <div class="code-hero-display">
                                <div class="hud-corners" aria-hidden="true">
                                    <span></span><span></span><span></span><span></span>
                                </div>
                                <div class="scan-line" aria-hidden="true"></div>
                                <div class="code-screen-top">
                                    <span></span><span></span><span></span>
                                </div>
                                <div class="code-screen-body">
                                    <div class="hero-left-panel" aria-hidden="true">
                                        <div class="hlp-header">
                                            <span class="hlp-dot hlp-dot-on"></span>
                                            <span class="hlp-title">Sistema Live</span>
                                        </div>
                                        <div class="hlp-stats">
                                            <div class="hlp-stat">
                                                <span class="hlp-stat-val">27</span>
                                                <span class="hlp-stat-lbl">Clientes</span>
                                            </div>
                                            <div class="hlp-stat">
                                                <span class="hlp-stat-val hlp-purple">99%</span>
                                                <span class="hlp-stat-lbl">Uptime</span>
                                            </div>
                                        </div>
                                        <div class="hlp-chart">
                                            <svg viewBox="0 0 150 52" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                                <defs>
                                                    <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="0%" stop-color="#06b6d4" stop-opacity="0.35"/>
                                                        <stop offset="100%" stop-color="#06b6d4" stop-opacity="0"/>
                                                    </linearGradient>
                                                </defs>
                                                <path class="hlp-area" d="M0,46 L18,40 L36,34 L54,26 L72,30 L90,18 L108,11 L126,7 L150,3 L150,52 L0,52Z" fill="url(#areaGrad)"/>
                                                <path class="hlp-line" d="M0,46 L18,40 L36,34 L54,26 L72,30 L90,18 L108,11 L126,7 L150,3" fill="none" stroke="#22d3ee" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                <circle class="hlp-radar" cx="150" cy="3" r="3" fill="none" stroke="#22d3ee" stroke-width="1"/>
                                                <circle cx="150" cy="3" r="3" fill="#22d3ee" class="hlp-dot-pulse"/>
                                            </svg>
                                            <span class="hlp-chart-lbl">ventas / mes ↑</span>
                                        </div>
                                        <div class="hlp-code">
                                            <div class="cs-line"><span class="cs-kw">await</span> <span class="cs-fn">deploy</span>({ <span class="cs-prop">env</span>: <span class="cs-bool">'prod'</span> })</div>
                                            <div class="cs-line"><span class="cs-cm">// ✓ 3 módulos activos</span></div>
                                            <div class="cs-line cs-cursor">█</div>
                                        </div>
                                        <div class="hlp-pills">
                                            <span class="hlp-pill hlp-on">● API</span>
                                            <span class="hlp-pill hlp-on">● DB</span>
                                            <span class="hlp-pill hlp-on">● Bot</span>
                                        </div>
                                    </div>
                                    <div class="code-lines-right">
                                        <s></s><s></s><s></s><s></s><s></s>
                                    </div>
                                </div>
                            </div>
                            <div class="code-symbol-overlay" aria-hidden="true">&lt;/&gt;</div>
                            <div class="dashboard-preview">
                                <div class="preview-header">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <div class="preview-grid">
                                    <div class="preview-panel wide">
                                        <div class="preview-label">
                                            <ReceiptText class="preview-icon" />
                                            SaaS activo
                                        </div>
                                        <strong>27 clientes</strong>
                                        <div class="preview-bars">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                    <div class="preview-panel">
                                        <div class="preview-label">
                                            <Bot class="preview-icon" />
                                            Chatbot
                                        </div>
                                        <strong>Online</strong>
                                    </div>
                                    <div class="preview-panel">
                                        <div class="preview-label">
                                            <ShieldCheck class="preview-icon" />
                                            Seguro
                                        </div>
                                        <strong>Activo</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-module-cloud">
                                <span v-for="module in liveModules.slice(0, 4)" :key="module.label">
                                    <component :is="module.icon" class="module-cloud-icon" />
                                    {{ module.label }}
                                </span>
                            </div>
                            <div class="floating-icon icon-1">
                                <ChartPie class="floating-svg" />
                            </div>
                            <div class="floating-icon icon-2">
                                <ShieldCheck class="floating-svg" />
                            </div>
                            <div class="floating-icon icon-3">
                                <Zap class="floating-svg" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-fade" aria-hidden="true"></div>
            </section>

            <section class="proof-strip" aria-label="Proceso de implementación">
                <div class="container proof-grid">
                    <article
                        v-for="(step, index) in workflowSteps"
                        :key="step.title"
                        class="proof-item reveal"
                        :class="`delay-${Math.min(index, 3)}`"
                    >
                        <div class="proof-icon">
                            <component :is="step.icon" class="proof-svg" />
                        </div>
                        <div>
                            <h2>{{ step.title }}</h2>
                            <p>{{ step.description }}</p>
                        </div>
                    </article>
                </div>
            </section>

            <section class="impact-band" aria-label="Diagnóstico rápido por WhatsApp">
                <div class="container impact-panel reveal">
                    <div class="impact-copy">
                        <span>Diagnóstico gratis</span>
                        <h2>Cuéntanos tu negocio y te decimos qué sistema necesitas</h2>
                        <p>
                            En pocos minutos revisamos tu rubro, procesos y puntos de dolor para
                            proponerte un sistema SaaS, chatbot o desarrollo a medida.
                        </p>
                    </div>

                    <div class="impact-stats" aria-label="Ventajas del diagnóstico">
                        <div v-for="stat in impactStats" :key="stat.label" class="impact-stat">
                            <strong>{{ stat.value }}</strong>
                            <span>{{ stat.label }}</span>
                        </div>
                    </div>

                    <a
                        class="impact-whatsapp"
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <MessageCircle class="impact-whatsapp-icon" />
                        Hablar ahora
                    </a>
                </div>
            </section>

            <section id="saas" class="section saas-showcase">
                <div class="container">
                    <div class="section-title">
                        <span>SaaS, chatbot y desarrollo</span>
                        <h2>Una plataforma pensada para vender más y operar mejor</h2>
                        <p>
                            No solo hacemos páginas. Construimos sistemas que trabajan contigo:
                            cobran, responden, reportan, ordenan y escalan.
                        </p>
                    </div>

                    <div class="service-pillar-grid">
                        <article
                            v-for="(pillar, index) in servicePillars"
                            :key="pillar.title"
                            class="service-pillar reveal"
                            :class="`delay-${Math.min(index, 3)}`"
                        >
                            <div class="pillar-topline">
                                <div class="pillar-icon">
                                    <component :is="pillar.icon" class="pillar-svg" />
                                </div>
                                <span>{{ pillar.eyebrow }}</span>
                            </div>
                            <h3>{{ pillar.title }}</h3>
                            <p>{{ pillar.description }}</p>
                            <ul>
                                <li v-for="point in pillar.points" :key="point">
                                    <CheckCircle2 class="check-icon" />
                                    {{ point }}
                                </li>
                            </ul>
                        </article>
                    </div>

                    <div class="operations-panel reveal delay-1">
                        <div class="operations-copy">
                            <span>Centro de control</span>
                            <h3>Todo tu negocio en una sola pantalla</h3>
                            <p>
                                Tu equipo ve ventas, stock, clientes, pagos, citas y conversaciones
                                sin saltar entre herramientas. Menos caos, más decisiones rápidas.
                            </p>
                        </div>
                        <div class="live-module-grid" aria-label="Módulos disponibles">
                            <article v-for="module in liveModules" :key="module.label" class="live-module">
                                <component :is="module.icon" class="live-module-icon" />
                                <div>
                                    <strong>{{ module.value }}</strong>
                                    <span>{{ module.label }}</span>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </section>

            <section id="soluciones" class="section business-solutions">
                <div class="container">
                    <div class="section-title">
                        <span>Nuestros Servicios</span>
                        <h2>Soluciones para tu Negocio</h2>
                        <p>
                            Sistemas especializados diseñados para las necesidades específicas de
                            cada tipo de negocio.
                        </p>
                    </div>

                    <div class="solutions-grid">
                        <article
                            v-for="(solution, index) in solutions"
                            :key="solution.key"
                            class="solution-card reveal"
                            :class="[solution.key, `delay-${Math.min(index, 3)}`]"
                        >
                            <div class="solution-icon-wrapper">
                                <component :is="solution.icon" class="card-icon" />
                            </div>
                            <h3>{{ solution.title }}</h3>
                            <p>{{ solution.description }}</p>
                            <button
                                type="button"
                                class="btn btn-sm"
                                :class="solution.buttonClass"
                                @click="scrollToSection('#contacto')"
                            >
                                Solicitar Demo
                            </button>
                        </article>
                    </div>
                </div>
            </section>

            <section id="beneficios" class="section features">
                <div class="container">
                    <div class="section-title">
                        <span>Por qué elegirnos</span>
                        <h2>Beneficios Clave</h2>
                        <p>Lo que nuestros sistemas pueden hacer por tu negocio.</p>
                    </div>

                    <div class="features-list">
                        <article
                            v-for="(feature, index) in features"
                            :key="feature.title"
                            class="feature-item reveal"
                            :class="`delay-${Math.min(index, 3)}`"
                        >
                            <div class="feature-icon">
                                <component :is="feature.icon" class="feature-svg" />
                            </div>
                            <div class="feature-text">
                                <h3>{{ feature.title }}</h3>
                                <p>{{ feature.description }}</p>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="portafolio" class="section portfolio">
                <div class="container">
                    <div class="section-title">
                        <span>Portafolio</span>
                        <h2>Casos de Éxito</h2>
                        <p>Algunos ejemplos de sistemas implementados para nuestros clientes.</p>
                    </div>

                    <div class="business-type-selector">
                        <div class="business-type-buttons" role="list" aria-label="Filtrar casos de éxito">
                            <button
                                v-for="filter in filters"
                                :key="filter.value"
                                type="button"
                                class="filter-btn"
                                :class="{ active: selectedFilter === filter.value }"
                                @click="selectedFilter = filter.value"
                            >
                                <component v-if="filter.icon" :is="filter.icon" class="filter-icon" />
                                {{ filter.label }}
                            </button>
                        </div>
                    </div>

                    <div class="portfolio-grid">
                        <article
                            v-for="item in filteredPortfolioItems"
                            :key="item.title"
                            class="portfolio-item reveal"
                        >
                            <div class="portfolio-img">
                                <img :src="item.image" :alt="item.alt" />
                                <span class="portfolio-category" :class="item.badgeClass">
                                    {{ item.categoryLabel }}
                                </span>
                            </div>
                            <div class="portfolio-info">
                                <h3>{{ item.title }}</h3>
                                <p>{{ item.description }}</p>
                                <div class="portfolio-links">
                                    <button type="button" @click="scrollToSection('#contacto')">
                                        <Images class="inline-icon" />
                                        Ver capturas
                                    </button>
                                    <a
                                        v-if="item.link"
                                        :href="item.link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="portfolio-link-live"
                                    >
                                        <ExternalLink class="inline-icon" />
                                        Ver sitio
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="testimonios" class="section testimonials">
                <div class="container">
                    <div class="section-title">
                        <span>Testimonios</span>
                        <h2>Lo que dicen nuestros clientes</h2>
                        <p>Testimonios de negocios que han transformado sus operaciones.</p>
                    </div>

                    <div class="testimonials-grid">
                        <article
                            v-for="(testimonial, index) in testimonials"
                            :key="testimonial.name"
                            class="testimonial-card reveal"
                            :class="`delay-${Math.min(index, 3)}`"
                        >
                            <div class="testimonial-content">
                                <p>{{ testimonial.quote }}</p>
                            </div>
                            <div class="testimonial-author">
                                <img :src="testimonial.image" :alt="testimonial.name" />
                                <div class="author-info">
                                    <h4>{{ testimonial.name }}</h4>
                                    <p>{{ testimonial.role }}</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="planes" class="section pricing">
                <div class="container">
                    <div class="section-title">
                        <span>Planes flexibles</span>
                        <h2>Empieza pequeño, escala cuando vendas más</h2>
                        <p>
                            Elige un plan base y lo adaptamos a tu rubro, equipo y forma de trabajo.
                        </p>
                    </div>

                    <div class="pricing-grid">
                        <article
                            v-for="(plan, index) in plans"
                            :key="plan.name"
                            class="pricing-card reveal"
                            :class="[{ highlighted: plan.highlighted }, `delay-${Math.min(index, 3)}`]"
                        >
                            <div class="pricing-header">
                                <span v-if="plan.highlighted" class="popular-badge">Más pedido</span>
                                <h3>{{ plan.name }}</h3>
                                <strong>{{ plan.price }}</strong>
                                <p>{{ plan.description }}</p>
                            </div>
                            <ul>
                                <li v-for="feature in plan.features" :key="feature">
                                    <CheckCircle2 class="check-icon" />
                                    {{ feature }}
                                </li>
                            </ul>
                            <button type="button" class="btn btn-sm" @click="scrollToSection('#contacto')">
                                Cotizar ahora
                            </button>
                        </article>
                    </div>
                </div>
            </section>

            <section id="contacto" class="section contact">
                <div class="container">
                    <div class="section-title">
                        <span>Contáctanos</span>
                        <h2>¿Listo para transformar tu negocio?</h2>
                        <p>Contáctanos para una demostración personalizada.</p>
                    </div>

                    <div class="contact-form reveal">
                        <form @submit.prevent="submit">
                            <div class="form-group">
                                <label for="name">Nombre</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="form-control"
                                    autocomplete="name"
                                    required
                                />
                                <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="form-control"
                                    autocomplete="email"
                                    required
                                />
                                <p v-if="form.errors.email" class="form-error">{{ form.errors.email }}</p>
                            </div>

                            <div class="form-group">
                                <label for="phone">Teléfono</label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    class="form-control"
                                    autocomplete="tel"
                                    required
                                />
                                <p v-if="form.errors.phone" class="form-error">{{ form.errors.phone }}</p>
                            </div>

                            <div class="form-group">
                                <label for="business-type">Tipo de Negocio</label>
                                <select
                                    id="business-type"
                                    v-model="form.business_type"
                                    class="form-control"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="restaurante">Restaurante/Cafetería</option>
                                    <option value="tienda">Tienda/Comercio</option>
                                    <option value="farmacia">Farmacia</option>
                                    <option value="ferreteria">Ferretería</option>
                                    <option value="veterinaria">Clínica Veterinaria</option>
                                    <option value="saas">Distribuidor SaaS</option>
                                    <option value="chatbot">Chatbot / Automatización</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <p v-if="form.errors.business_type" class="form-error">
                                    {{ form.errors.business_type }}
                                </p>
                            </div>

                            <div class="form-group">
                                <label for="message">¿Qué desafíos enfrenta tu negocio?</label>
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    class="form-control"
                                    required
                                ></textarea>
                                <p v-if="form.errors.message" class="form-error">
                                    {{ form.errors.message }}
                                </p>
                            </div>

                            <button type="submit" class="btn submit-button" :disabled="form.processing">
                                {{ form.processing ? 'Enviando...' : 'Solicitar Demostración' }}
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <div class="container">
                <div class="social-links">
                    <button type="button" aria-label="WhatsApp" @click="scrollToSection('#contacto')">
                        <MessageCircle class="social-icon" />
                    </button>
                    <button type="button" aria-label="Facebook" @click="scrollToSection('#contacto')">
                        <Facebook class="social-icon" />
                    </button>
                    <button type="button" aria-label="Instagram" @click="scrollToSection('#contacto')">
                        <Instagram class="social-icon" />
                    </button>
                    <button type="button" aria-label="Email" @click="scrollToSection('#contacto')">
                        <Mail class="social-icon" />
                    </button>
                </div>
                <p>
                    &copy; {{ new Date().getFullYear() }} Solución Digital.
                    Todos los derechos reservados.
                </p>
                <p class="footer-note">
                    Alquiler de sistemas SaaS, distribuidores, chatbot y software a medida para negocios.
                </p>
            </div>
        </footer>

        <div
            v-if="loginModalOpen"
            class="login-modal-backdrop"
            tabindex="-1"
            role="presentation"
            @click.self="closeLoginModal"
            @keydown.esc="closeLoginModal"
        >
            <section
                class="login-modal reveal is-visible"
                role="dialog"
                aria-modal="true"
                aria-labelledby="login-modal-title"
            >
                <button
                    type="button"
                    class="modal-close"
                    aria-label="Cerrar inicio de sesión"
                    @click="closeLoginModal"
                >
                    <X class="icon" />
                </button>

                <div class="login-modal-header">
                    <div class="login-modal-icon">
                        <ShieldCheck class="modal-icon-svg" />
                    </div>
                    <div>
                        <p>Panel administrativo</p>
                        <h2 id="login-modal-title">Iniciar sesión</h2>
                    </div>
                </div>

                <form class="login-modal-form" @submit.prevent="submitLogin">
                    <div class="form-group">
                        <label for="login-email">Correo electrónico</label>
                        <input
                            id="login-email"
                            v-model="loginForm.email"
                            type="email"
                            class="form-control"
                            autocomplete="email"
                            placeholder="email@ejemplo.com"
                            required
                            autofocus
                        />
                        <p v-if="loginForm.errors.email" class="form-error">
                            {{ loginForm.errors.email }}
                        </p>
                    </div>

                    <div class="form-group">
                        <div class="login-label-row">
                            <label for="login-password">Contraseña</label>
                            <Link :href="passwordRequest()" class="forgot-link">
                                ¿Olvidaste?
                            </Link>
                        </div>
                        <input
                            id="login-password"
                            v-model="loginForm.password"
                            type="password"
                            class="form-control"
                            autocomplete="current-password"
                            placeholder="Tu contraseña"
                            required
                        />
                        <p v-if="loginForm.errors.password" class="form-error">
                            {{ loginForm.errors.password }}
                        </p>
                    </div>

                    <label class="remember-row">
                        <input v-model="loginForm.remember" type="checkbox" />
                        <span>Recordarme en este equipo</span>
                    </label>

                    <button type="submit" class="btn submit-button" :disabled="loginForm.processing">
                        {{ loginForm.processing ? 'Ingresando...' : 'Entrar al dashboard' }}
                    </button>
                </form>
            </section>
        </div>

        <a
            class="whatsapp-float"
            :href="whatsappUrl"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Contactar por WhatsApp al +591 77149775"
        >
            <MessageCircle class="whatsapp-float-icon" />
            <span>WhatsApp</span>
        </a>

        <Toaster />
    </div>
</template>

<style scoped>
:global(html) {
    scroll-behavior: smooth;
}

.landing-page {
    --primary-color: #06b6d4;
    --primary-dark: #0e7490;
    --secondary-color: #a855f7;
    --dark-bg: #080c14;
    --card-bg: #111827;
    --text-main: #e2e8f0;
    --text-light: #94a3b8;
    --light-bg: #0d1117;
    --restaurant-color: #f43f5e;
    --retail-color: #10b981;
    --pharmacy-color: #a855f7;
    --hardware-color: #f59e0b;
    --vet-color: #0d9488;
    --sport-color: #f97316;
    --saas-color: #06b6d4;
    --chatbot-color: #22d3ee;
    --border: rgb(255 255 255 / 8%);
    --glow-cyan: 0 0 20px rgb(6 182 212 / 30%);
    --glow-purple: 0 0 20px rgb(168 85 247 / 30%);

    min-height: 100vh;
    overflow-x: hidden;
    background: var(--dark-bg);
    color: var(--text-main);
    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
    line-height: 1.6;
}

.landing-page *,
.landing-page *::before,
.landing-page *::after {
    box-sizing: border-box;
}

.landing-page h1,
.landing-page h2,
.landing-page h3,
.landing-page h4,
.landing-page h5,
.landing-page h6 {
    margin: 0;
    color: #ffffff;
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
}

.landing-page p {
    margin: 0;
    overflow-wrap: anywhere;
}

.landing-page button {
    font: inherit;
}

.landing-page img {
    max-width: 100%;
}

.container {
    width: min(1280px, calc(100% - 48px));
    margin: 0 auto;
}

header {
    position: fixed;
    z-index: 1000;
    top: 0;
    left: 0;
    width: 100%;
    border-bottom: 1px solid rgb(6 182 212 / 12%);
    background-color: rgb(8 12 20 / 92%);
    backdrop-filter: blur(16px);
    transition:
        padding 0.3s ease,
        box-shadow 0.3s ease;
}

header.scrolled {
    box-shadow: 0 4px 24px rgb(6 182 212 / 8%), 0 2px 8px rgb(0 0 0 / 40%);
}

.scroll-progress {
    position: absolute;
    bottom: -1px;
    left: 0;
    height: 3px;
    background: linear-gradient(90deg, #06b6d4, #a855f7, #06b6d4);
    box-shadow: 0 0 12px rgb(6 182 212 / 60%);
    transition: width 0.12s linear;
}

nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    min-height: 86px;
}

.logo-button {
    display: inline-flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    border: 0;
    background: transparent;
    cursor: pointer;
    line-height: 0;
}

.brand-logo {
    display: block;
    width: 68px;
    height: 68px;
    border-radius: 12px;
    object-fit: contain;
}

.nav-links {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    gap: 20px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.nav-actions {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    gap: 12px;
}

.nav-actions .btn {
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 700;
    min-height: unset;
}


.mobile-actions {
    display: none;
    align-items: center;
    gap: 10px;
}

.nav-links button:not(.btn),
.mobile-menu button:not(.btn) {
    position: relative;
    border: 0;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    font-size: 15px;
    font-weight: 500;
    transition: color 0.3s;
}

.login-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 30px;
    border: 1px solid rgb(6 182 212 / 30%);
    border-radius: 999px;
    background: rgb(6 182 212 / 8%);
    color: #22d3ee;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 13px;
    text-decoration: none;
    transition:
        transform 0.3s ease,
        background 0.3s ease,
        border-color 0.3s ease,
        box-shadow 0.3s ease;
}

.nav-actions .login-cta,
.mobile-menu .login-cta {
    border: 0;
    background: linear-gradient(135deg, #06b6d4, #0e7490);
    box-shadow: 0 4px 20px rgb(6 182 212 / 30%);
    color: #ffffff;
}

.login-link:hover {
    border-color: #22d3ee;
    background: rgb(6 182 212 / 15%);
    box-shadow: var(--glow-cyan);
    transform: translateY(-2px);
}

.nav-actions .login-cta:hover,
.mobile-menu .login-cta:hover {
    background: linear-gradient(135deg, #22d3ee, #06b6d4);
    box-shadow: 0 8px 28px rgb(6 182 212 / 40%);
    color: #ffffff;
    transform: translateY(-2px);
}

.mobile-login {
    width: 100%;
}

.nav-links button:not(.btn)::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #06b6d4, #a855f7);
    transition: width 0.3s;
}

.nav-links button:not(.btn):hover::after {
    width: 100%;
}

.nav-links button:not(.btn):hover,
.mobile-menu button:not(.btn):hover {
    color: #22d3ee;
}

.mobile-menu-button {
    display: none;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border: 1px solid rgb(6 182 212 / 20%);
    border-radius: 8px;
    background: rgb(6 182 212 / 6%);
    color: #22d3ee;
    cursor: pointer;
}

.mobile-menu {
    display: none;
    padding: 0 0 18px;
}

.icon,
.badge-icon,
.filter-icon,
.inline-icon,
.social-icon,
.btn-icon {
    width: 18px;
    height: 18px;
}

.hero {
    position: relative;
    display: flex;
    min-height: 100vh;
    align-items: center;
    overflow: hidden;
    padding: 112px 0 78px;
    background-color: var(--dark-bg);
    background-image:
        radial-gradient(at 0% 0%, rgb(6 182 212 / 12%) 0, transparent 50%),
        radial-gradient(at 100% 0%, rgb(168 85 247 / 12%) 0, transparent 50%),
        radial-gradient(at 50% 100%, rgb(6 182 212 / 6%) 0, transparent 40%);
}

.hero::before {
    content: '';
    position: absolute;
    z-index: 0;
    inset: 0;
    background-image:
        linear-gradient(rgb(6 182 212 / 4%) 1px, transparent 1px),
        linear-gradient(90deg, rgb(6 182 212 / 4%) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: linear-gradient(to bottom, rgb(0 0 0 / 100%) 60%, rgb(0 0 0 / 0%));
}

.hero::after {
    content: '';
    position: absolute;
    z-index: 0;
    top: 0;
    left: -22%;
    width: 36%;
    height: 100%;
    background: linear-gradient(105deg, transparent, rgb(6 182 212 / 8%), transparent);
    filter: blur(2px);
    transform: skewX(-16deg);
    animation: heroSweep 8s ease-in-out infinite;
    pointer-events: none;
}

.hero-pixels {
    position: absolute;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    overflow: hidden;
}

.px {
    position: absolute;
    border-radius: 2px;
    animation: pixelFloat 4s ease-in-out infinite;
}

.px-c1 { width: 12px; height: 12px; top: 20%; left: 4%; background: #06b6d4; opacity: 0.8; animation-delay: 0s; }
.px-c2 { width: 8px; height: 8px; top: 35%; left: 7%; background: #22d3ee; opacity: 0.6; animation-delay: 0.5s; }
.px-c3 { width: 14px; height: 14px; top: 55%; left: 3%; background: #06b6d4; opacity: 0.7; animation-delay: 1s; }
.px-c4 { width: 6px; height: 6px; top: 70%; left: 9%; background: #22d3ee; opacity: 0.5; animation-delay: 1.5s; }
.px-c5 { width: 10px; height: 10px; top: 15%; left: 12%; background: #06b6d4; opacity: 0.4; animation-delay: 0.8s; }
.px-c6 { width: 8px; height: 8px; top: 80%; left: 6%; background: #22d3ee; opacity: 0.7; animation-delay: 2s; }
.px-c7 { width: 12px; height: 12px; top: 45%; left: 1%; background: #06b6d4; opacity: 0.5; animation-delay: 0.3s; }
.px-c8 { width: 6px; height: 6px; top: 60%; left: 14%; background: #22d3ee; opacity: 0.6; animation-delay: 1.8s; }
.px-p1 { width: 12px; height: 12px; top: 22%; right: 4%; background: #a855f7; opacity: 0.8; animation-delay: 0.4s; }
.px-p2 { width: 8px; height: 8px; top: 38%; right: 7%; background: #c084fc; opacity: 0.6; animation-delay: 0.9s; }
.px-p3 { width: 14px; height: 14px; top: 58%; right: 3%; background: #a855f7; opacity: 0.7; animation-delay: 1.4s; }
.px-p4 { width: 6px; height: 6px; top: 72%; right: 9%; background: #c084fc; opacity: 0.5; animation-delay: 0.2s; }
.px-p5 { width: 10px; height: 10px; top: 18%; right: 12%; background: #a855f7; opacity: 0.4; animation-delay: 1.2s; }
.px-p6 { width: 8px; height: 8px; top: 82%; right: 5%; background: #c084fc; opacity: 0.7; animation-delay: 2.4s; }
.px-p7 { width: 12px; height: 12px; top: 48%; right: 1%; background: #a855f7; opacity: 0.5; animation-delay: 0.7s; }
.px-p8 { width: 6px; height: 6px; top: 62%; right: 14%; background: #c084fc; opacity: 0.6; animation-delay: 2.2s; }

@keyframes pixelFloat {
    0%, 100% { transform: translateY(0); }
    33% { transform: translateY(-8px); }
    66% { transform: translateY(4px); }
}

.hero-container {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 48px;
    align-items: center;
    width: min(1280px, calc(100% - 48px));
    margin: 0 auto;
}

.hero-text {
    text-align: left;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    max-width: 100%;
    margin-bottom: 24px;
    padding: 8px 16px;
    border: 1px solid rgb(6 182 212 / 25%);
    border-radius: 999px;
    background: rgb(6 182 212 / 10%);
    color: #22d3ee;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.35;
    backdrop-filter: blur(5px);
    box-shadow: 0 0 20px rgb(6 182 212 / 10%);
}

.hero-text h1 {
    margin-bottom: 24px;
    color: #ffffff;
    font-size: 52px;
    font-weight: 800;
    letter-spacing: 0;
    line-height: 1.1;
}

.hero-text h1 span {
    background: linear-gradient(135deg, #60a5fa 0%, #a855f7 100%);
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-text p {
    max-width: 540px;
    margin-bottom: 24px;
    color: #94a3b8;
    font-size: 18px;
    line-height: 1.7;
}

.hero-tech-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 34px;
}

.hero-tech-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 1px solid rgb(255 255 255 / 12%);
    border-radius: 999px;
    background: rgb(255 255 255 / 7%);
    color: #dbeafe;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.3;
    padding: 9px 13px;
    backdrop-filter: blur(12px);
}

.hero-tech-icon {
    width: 16px;
    height: 16px;
    color: #22d3ee;
}

.hero-btns {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.hero-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    max-width: 560px;
    margin-top: 34px;
}

.hero-stat {
    border: 1px solid rgb(6 182 212 / 15%);
    border-radius: 8px;
    background: rgb(6 182 212 / 6%);
    padding: 14px 16px;
    backdrop-filter: blur(12px);
}

.hero-stat strong {
    display: block;
    color: #ffffff;
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 24px;
    line-height: 1;
}

.hero-stat span {
    display: block;
    margin-top: 6px;
    color: #22d3ee;
    font-size: 13px;
    font-weight: 600;
}

.btn,
.btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    border-radius: 999px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease,
        background 0.3s ease,
        border-color 0.3s ease;
}

.btn {
    border: 0;
    background: linear-gradient(135deg, #06b6d4, #0e7490);
    box-shadow: 0 8px 24px rgb(6 182 212 / 35%);
    color: #ffffff;
    padding: 12px 28px;
}

.btn:hover {
    background: linear-gradient(135deg, #22d3ee, #06b6d4);
    box-shadow: 0 12px 32px rgb(6 182 212 / 50%);
    transform: translateY(-4px);
}

.btn:disabled {
    cursor: wait;
    opacity: 0.65;
    transform: none;
}

.btn-outline {
    border: 2px solid rgb(6 182 212 / 30%);
    background: transparent;
    color: #22d3ee;
    padding: 10px 26px;
}

.btn-outline:hover {
    border-color: #06b6d4;
    background: rgb(6 182 212 / 10%);
    box-shadow: var(--glow-cyan);
    transform: translateY(-4px);
}

.btn-sm {
    min-height: 30px;
    padding: 6px 14px;
    font-size: 12px;
}

.btn-panel {
    background: linear-gradient(135deg, #a855f7, #7c3aed);
    box-shadow: 0 4px 20px rgb(168 85 247 / 35%);
    text-decoration: none;
}

.btn-panel:hover {
    background: linear-gradient(135deg, #c084fc, #a855f7);
    box-shadow: 0 8px 28px rgb(168 85 247 / 45%);
}

.btn-restaurant {
    background: var(--restaurant-color);
    box-shadow: 0 4px 15px rgb(244 63 94 / 30%);
}

.btn-restaurant:hover {
    background: #e11d48;
}

.btn-retail {
    background: var(--retail-color);
    box-shadow: 0 4px 15px rgb(16 185 129 / 30%);
}

.btn-retail:hover {
    background: #059669;
}

.btn-pharmacy {
    background: var(--pharmacy-color);
    box-shadow: 0 4px 15px rgb(168 85 247 / 30%);
}

.btn-pharmacy:hover {
    background: #9333ea;
}

.btn-hardware {
    background: var(--hardware-color);
    box-shadow: 0 4px 15px rgb(245 158 11 / 30%);
}

.btn-hardware:hover {
    background: #d97706;
}

.btn-vet {
    background: var(--vet-color);
    box-shadow: 0 4px 15px rgb(13 148 136 / 30%);
}

.btn-vet:hover {
    background: #0f766e;
}

.btn-saas {
    background: var(--saas-color);
    box-shadow: 0 4px 15px rgb(6 182 212 / 30%);
}

.btn-saas:hover {
    background: #0891b2;
}

.btn-chatbot {
    background: var(--chatbot-color);
    box-shadow: 0 4px 15px rgb(34 211 238 / 30%);
}

.btn-chatbot:hover {
    background: #06b6d4;
}

.hero-visual {
    position: relative;
    perspective: 1000px;
    transform:
        translate3d(calc(var(--pointer-x) * 8px), calc(var(--pointer-y) * 8px), 0)
        rotateX(calc(var(--pointer-y) * -2deg))
        rotateY(calc(var(--pointer-x) * 2deg));
    transition: transform 0.18s ease-out;
}

.visual-card {
    position: relative;
    border: 1px solid rgb(6 182 212 / 15%);
    border-radius: 12px;
    background: rgb(5 8 16 / 80%);
    box-shadow: 0 25px 50px -12px rgb(0 0 0 / 70%), 0 0 60px rgb(6 182 212 / 8%);
    padding: 15px;
    backdrop-filter: blur(10px);
    transform: rotateY(-8deg) rotateX(4deg);
    transition: transform 0.5s ease;
}

.visual-card:hover {
    transform: rotateY(0deg) rotateX(0deg);
}

.code-hero-display {
    width: 100%;
    border-radius: 8px;
    background:
        radial-gradient(ellipse at 15% 60%, rgb(6 182 212 / 20%) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 40%, rgb(168 85 247 / 16%) 0%, transparent 50%),
        radial-gradient(ellipse at 50% 100%, rgb(6 182 212 / 10%) 0%, transparent 45%),
        linear-gradient(rgb(6 182 212 / 12%) 1px, transparent 1px),
        linear-gradient(90deg, rgb(6 182 212 / 12%) 1px, transparent 1px),
        #050810;
    background-size: 100% 100%, 100% 100%, 100% 100%, 36px 36px, 36px 36px;
    border: 1px solid rgb(6 182 212 / 12%);
    overflow: hidden;
    box-shadow: inset 0 0 60px rgb(0 0 0 / 50%);
    aspect-ratio: 4 / 3;
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 2;
}

.code-screen-top {
    display: flex;
    gap: 7px;
    padding: 12px 14px;
    border-bottom: 1px solid rgb(255 255 255 / 5%);
    background: rgb(255 255 255 / 2%);
    flex-shrink: 0;
}

.code-screen-top span {
    width: 9px;
    height: 9px;
    border-radius: 50%;
}

.code-screen-top span:nth-child(1) { background: #06b6d4; box-shadow: 0 0 6px #06b6d4; }
.code-screen-top span:nth-child(2) { background: #a855f7; box-shadow: 0 0 6px #a855f7; }
.code-screen-top span:nth-child(3) { background: rgb(255 255 255 / 20%); }

.code-screen-body {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 20px;
}

.code-lines-left,
.code-lines-right {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.code-lines-left { left: 16px; }
.code-lines-right { right: 16px; align-items: flex-end; }

.hero-left-panel {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    width: 160px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.hlp-header {
    display: flex;
    align-items: center;
    gap: 6px;
}

.hlp-dot-on {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #22d3ee;
    box-shadow: 0 0 6px #22d3ee;
    animation: cursorBlink 2s ease-in-out infinite;
    flex-shrink: 0;
}

.hlp-title {
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.hlp-stats {
    display: flex;
    gap: 10px;
}

.hlp-stat {
    display: flex;
    flex-direction: column;
    gap: 1px;
    background: rgb(6 182 212 / 8%);
    border: 1px solid rgb(6 182 212 / 15%);
    border-radius: 6px;
    padding: 6px 10px;
    flex: 1;
}

.hlp-stat-val {
    font-size: 18px;
    font-weight: 800;
    color: #22d3ee;
    line-height: 1;
}

.hlp-purple { color: #a855f7 !important; }

.hlp-stat-lbl {
    font-size: 9px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.hlp-chart {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.hlp-chart svg {
    width: 100%;
    height: 44px;
    border-radius: 4px;
    overflow: visible;
}

.hlp-dot-pulse {
    animation: dotPulse 2s ease-in-out infinite;
}

@keyframes dotPulse {
    0%, 100% { r: 3; opacity: 1; }
    50% { r: 4.5; opacity: 0.7; }
}

.hlp-radar {
    animation: radarRing 2s ease-out infinite;
    transform-origin: 150px 3px;
}

@keyframes radarRing {
    0%   { r: 3;  opacity: 0.9; stroke-width: 1.5; }
    100% { r: 14; opacity: 0;   stroke-width: 0.3; }
}

.hlp-line {
    stroke-dasharray: 260;
    stroke-dashoffset: 260;
    animation: drawChart 2s ease-out forwards;
}

@keyframes drawChart {
    to { stroke-dashoffset: 0; }
}

.hud-corners {
    position: absolute;
    inset: 6px;
    pointer-events: none;
    z-index: 6;
}

.hud-corners span {
    position: absolute;
    width: 14px;
    height: 14px;
    border-color: rgb(6 182 212 / 55%);
    border-style: solid;
}

.hud-corners span:nth-child(1) { top: 0;    left: 0;  border-width: 2px 0 0 2px; }
.hud-corners span:nth-child(2) { top: 0;    right: 0; border-width: 2px 2px 0 0; }
.hud-corners span:nth-child(3) { bottom: 0; left: 0;  border-width: 0 0 2px 2px; }
.hud-corners span:nth-child(4) { bottom: 0; right: 0; border-width: 0 2px 2px 0; }

.scan-line {
    position: absolute;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgb(6 182 212 / 70%) 40%, rgb(168 85 247 / 50%) 60%, transparent 100%);
    box-shadow: 0 0 8px rgb(6 182 212 / 40%);
    animation: scanDown 4s linear infinite;
    z-index: 5;
    pointer-events: none;
}

@keyframes scanDown {
    0%   { top: 36px;  opacity: 0; }
    5%   { opacity: 1; }
    95%  { opacity: 1; }
    100% { top: 100%;  opacity: 0; }
}

.hlp-chart-lbl {
    font-size: 9px;
    color: #22d3ee;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.7;
}

.hlp-code {
    font-family: 'Courier New', Courier, monospace;
    font-size: 10px;
    line-height: 1.7;
    background: rgb(0 0 0 / 30%);
    border: 1px solid rgb(255 255 255 / 5%);
    border-radius: 4px;
    padding: 6px 8px;
}

.hlp-pills {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.hlp-pill {
    font-size: 9px;
    font-weight: 700;
    padding: 3px 7px;
    border-radius: 999px;
    letter-spacing: 0.3px;
}

.hlp-on {
    background: rgb(6 182 212 / 12%);
    border: 1px solid rgb(6 182 212 / 30%);
    color: #22d3ee;
}

.cs-line { white-space: nowrap; color: #94a3b8; font-size: 10px; }
.cs-i { padding-left: 12px; }

.cs-kw   { color: #22d3ee; }
.cs-fn   { color: #a78bfa; }
.cs-var  { color: #e2e8f0; }
.cs-cm   { color: #475569; font-style: italic; }
.cs-prop { color: #7dd3fc; }
.cs-bool { color: #f472b6; }

.cs-cursor {
    color: #22d3ee;
    animation: cursorBlink 1.1s step-start infinite;
}

@keyframes cursorBlink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

.code-lines-left s,
.code-lines-right s {
    display: block;
    height: 7px;
    border-radius: 4px;
    text-decoration: none;
    animation: lineGlow 2.5s ease-in-out infinite;
}

.code-lines-left s:nth-child(1) { width: 56px; background: linear-gradient(90deg, rgb(6 182 212 / 80%), transparent); }
.code-lines-left s:nth-child(2) { width: 40px; background: linear-gradient(90deg, rgb(6 182 212 / 60%), transparent); animation-delay: 0.3s; }
.code-lines-left s:nth-child(3) { width: 48px; background: linear-gradient(90deg, rgb(6 182 212 / 70%), transparent); animation-delay: 0.6s; }
.code-lines-left s:nth-child(4) { width: 32px; background: linear-gradient(90deg, rgb(6 182 212 / 50%), transparent); animation-delay: 0.9s; }
.code-lines-left s:nth-child(5) { width: 44px; background: linear-gradient(90deg, rgb(6 182 212 / 65%), transparent); animation-delay: 1.2s; }
.code-lines-right s:nth-child(1) { width: 56px; background: linear-gradient(270deg, rgb(168 85 247 / 80%), transparent); }
.code-lines-right s:nth-child(2) { width: 40px; background: linear-gradient(270deg, rgb(168 85 247 / 60%), transparent); animation-delay: 0.3s; }
.code-lines-right s:nth-child(3) { width: 48px; background: linear-gradient(270deg, rgb(168 85 247 / 70%), transparent); animation-delay: 0.6s; }
.code-lines-right s:nth-child(4) { width: 32px; background: linear-gradient(270deg, rgb(168 85 247 / 50%), transparent); animation-delay: 0.9s; }
.code-lines-right s:nth-child(5) { width: 44px; background: linear-gradient(270deg, rgb(168 85 247 / 65%), transparent); animation-delay: 1.2s; }

.code-symbol-overlay {
    position: absolute;
    top: 42%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10;
    font-family: 'Courier New', 'Courier', monospace;
    font-size: clamp(52px, 10vw, 100px);
    font-weight: 900;
    background: linear-gradient(135deg, #22d3ee 0%, #a855f7 100%);
    background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1;
    user-select: none;
    pointer-events: none;
    animation: symbolPulse 4s ease-in-out infinite;
    white-space: nowrap;
}

@keyframes lineGlow {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

@keyframes symbolPulse {
    0%, 100% { filter: drop-shadow(0 0 20px rgb(6 182 212 / 50%)) drop-shadow(0 0 40px rgb(168 85 247 / 25%)); }
    50% { filter: drop-shadow(0 0 35px rgb(6 182 212 / 70%)) drop-shadow(0 0 60px rgb(168 85 247 / 45%)); }
}

.dashboard-preview {
    position: absolute;
    right: -24px;
    bottom: -24px;
    width: min(72%, 320px);
    border: 1px solid rgb(6 182 212 / 20%);
    border-radius: 8px;
    background: rgb(5 8 16 / 95%);
    box-shadow: 0 24px 60px rgb(0 0 0 / 60%), 0 0 30px rgb(6 182 212 / 15%);
    color: #ffffff;
    overflow: hidden;
    backdrop-filter: blur(18px);
    z-index: 3;
}

.hero-module-cloud {
    position: absolute;
    right: 28px;
    bottom: -66px;
    left: 28px;
    z-index: 4;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.hero-module-cloud span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid rgb(6 182 212 / 20%);
    border-radius: 999px;
    background: rgb(5 8 16 / 80%);
    color: #e0f2fe;
    font-size: 12px;
    font-weight: 800;
    padding: 8px 11px;
    backdrop-filter: blur(14px);
    animation: softBlink 3.2s ease-in-out infinite;
}

.hero-module-cloud span:nth-child(2) {
    animation-delay: 0.4s;
}

.hero-module-cloud span:nth-child(3) {
    animation-delay: 0.8s;
}

.hero-module-cloud span:nth-child(4) {
    animation-delay: 1.2s;
}

.module-cloud-icon {
    width: 14px;
    height: 14px;
    color: #22d3ee;
}

.preview-header {
    display: flex;
    gap: 7px;
    border-bottom: 1px solid rgb(255 255 255 / 8%);
    padding: 12px 14px;
}

.preview-header span {
    width: 9px;
    height: 9px;
    border-radius: 999px;
    background: #06b6d4;
}

.preview-header span:nth-child(2) {
    background: #a855f7;
}

.preview-header span:nth-child(3) {
    background: rgb(255 255 255 / 20%);
}

.preview-grid {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 10px;
    padding: 14px;
}

.preview-panel {
    min-height: 92px;
    border: 1px solid rgb(255 255 255 / 6%);
    border-radius: 8px;
    background: rgb(255 255 255 / 4%);
    padding: 14px;
}

.preview-panel.wide {
    grid-row: span 2;
}

.preview-label {
    display: flex;
    align-items: center;
    gap: 7px;
    color: #22d3ee;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
}

.preview-icon {
    width: 15px;
    height: 15px;
}

.preview-panel strong {
    display: block;
    margin-top: 10px;
    color: #ffffff;
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 26px;
}

.preview-bars {
    display: flex;
    align-items: end;
    gap: 8px;
    height: 74px;
    margin-top: 16px;
}

.preview-bars span {
    flex: 1;
    border-radius: 6px 6px 0 0;
    background: linear-gradient(180deg, #22d3ee, #06b6d4);
    animation: barPulse 2.8s ease-in-out infinite;
}

.preview-bars span:nth-child(1) {
    height: 48%;
}

.preview-bars span:nth-child(2) {
    height: 72%;
    animation-delay: 0.2s;
}

.preview-bars span:nth-child(3) {
    height: 58%;
    animation-delay: 0.4s;
}

.preview-bars span:nth-child(4) {
    height: 88%;
    animation-delay: 0.6s;
}

.floating-icon {
    position: absolute;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    border-radius: 10px;
    background: #111827;
    border: 1px solid rgb(255 255 255 / 10%);
    box-shadow: 0 10px 25px rgb(0 0 0 / 40%);
    animation: float 6s ease-in-out infinite;
}

.floating-svg {
    width: 28px;
    height: 28px;
}

.icon-1 {
    top: -30px;
    right: -30px;
    color: #22d3ee;
    border-color: rgb(6 182 212 / 30%);
}

.icon-2 {
    bottom: 40px;
    left: -40px;
    color: #a855f7;
    border-color: rgb(168 85 247 / 30%);
    animation-delay: 2s;
}

.icon-3 {
    top: 40%;
    right: -50px;
    color: #22d3ee;
    border-color: rgb(6 182 212 / 25%);
    animation-delay: 4s;
}

.hero-fade {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100px;
    background: linear-gradient(to top, #080c14, transparent);
    pointer-events: none;
}

@keyframes float {
    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-20px);
    }
}

@keyframes barPulse {
    0%,
    100% {
        filter: brightness(1);
        transform: scaleY(1);
    }

    50% {
        filter: brightness(1.25);
        transform: scaleY(0.82);
    }
}

@keyframes softBlink {
    0%,
    100% {
        border-color: rgb(6 182 212 / 20%);
        transform: translateY(0);
    }

    50% {
        border-color: rgb(6 182 212 / 50%);
        transform: translateY(-3px);
    }
}

@keyframes heroSweep {
    0%,
    18% {
        transform: translateX(0) skewX(-16deg);
        opacity: 0;
    }

    36% {
        opacity: 1;
    }

    54%,
    100% {
        transform: translateX(360%) skewX(-16deg);
        opacity: 0;
    }
}

.proof-strip {
    position: relative;
    z-index: 2;
    margin-top: -42px;
    background: var(--light-bg);
}

.proof-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.proof-item {
    display: flex;
    gap: 16px;
    align-items: flex-start;
    border: 1px solid rgb(6 182 212 / 12%);
    border-radius: 8px;
    background: rgb(255 255 255 / 3%);
    box-shadow: 0 20px 50px rgb(0 0 0 / 30%);
    padding: 22px;
    backdrop-filter: blur(14px);
}

.proof-icon {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 8px;
    background: rgb(6 182 212 / 10%);
    color: #22d3ee;
}

.proof-svg {
    width: 22px;
    height: 22px;
}

.proof-item h2 {
    margin-bottom: 6px;
    font-size: 18px;
    color: #e2e8f0;
}

.proof-item p {
    color: var(--text-light);
    font-size: 14px;
    line-height: 1.55;
}

.impact-band {
    position: relative;
    z-index: 1;
    padding: 42px 0 0;
    background: var(--dark-bg);
}

.impact-panel {
    position: relative;
    display: grid;
    grid-template-columns: 1fr 1.15fr auto;
    gap: 26px;
    align-items: center;
    overflow: hidden;
    border: 1px solid rgb(6 182 212 / 15%);
    border-radius: 8px;
    background: linear-gradient(135deg, rgb(13 17 23 / 98%), rgb(17 24 39 / 96%));
    box-shadow: 0 28px 80px rgb(0 0 0 / 30%), 0 0 40px rgb(6 182 212 / 5%);
    padding: 28px;
}

.impact-panel::before {
    content: '';
    position: absolute;
    top: -1px;
    right: 0;
    left: 0;
    height: 3px;
    background: linear-gradient(90deg, #06b6d4, #a855f7, #06b6d4);
    background-size: 220% 100%;
    animation: gradientShift 5s linear infinite;
    box-shadow: 0 0 12px rgb(6 182 212 / 50%);
}

.impact-copy {
    position: relative;
}

.impact-copy span {
    color: #22d3ee;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: 1.3px;
    text-transform: uppercase;
}

.impact-copy h2 {
    margin-top: 8px;
    color: #ffffff;
    font-size: 28px;
    line-height: 1.18;
}

.impact-copy p {
    margin-top: 12px;
    color: #cbd5e1;
    font-size: 14px;
    line-height: 1.65;
}

.impact-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
}

.impact-stat {
    border: 1px solid rgb(255 255 255 / 10%);
    border-radius: 8px;
    background: rgb(255 255 255 / 7%);
    padding: 14px;
}

.impact-stat strong {
    display: block;
    color: #ffffff;
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 18px;
    line-height: 1.05;
}

.impact-stat span {
    display: block;
    margin-top: 5px;
    color: #22d3ee;
    font-size: 12px;
    font-weight: 700;
    line-height: 1.35;
}

.impact-whatsapp {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 40px;
    white-space: nowrap;
    border-radius: 999px;
    background: linear-gradient(135deg, #25d366, #128c7e);
    box-shadow: 0 14px 30px rgb(18 140 126 / 28%);
    color: #ffffff;
    font-size: 14px;
    font-weight: 900;
    padding: 10px 18px;
    text-decoration: none;
    transition:
        box-shadow 0.25s ease,
        transform 0.25s ease;
}

.impact-whatsapp:hover {
    box-shadow: 0 18px 38px rgb(18 140 126 / 38%);
    transform: translateY(-2px);
}

.impact-whatsapp-icon {
    width: 18px;
    height: 18px;
}

@keyframes gradientShift {
    to {
        background-position: 220% 0;
    }
}

.saas-showcase {
    overflow: hidden;
    background: var(--light-bg);
    border-top: 1px solid rgb(255 255 255 / 4%);
}

.service-pillar-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
}

.service-pillar {
    position: relative;
    overflow: hidden;
    border: 1px solid rgb(255 255 255 / 8%);
    border-radius: 8px;
    background: rgb(255 255 255 / 3%);
    box-shadow: 0 16px 40px rgb(0 0 0 / 20%);
    padding: 30px;
    transition:
        border-color 0.3s ease,
        box-shadow 0.3s ease,
        transform 0.3s ease;
}

.service-pillar::after {
    content: '';
    position: absolute;
    right: -42px;
    bottom: -42px;
    width: 120px;
    height: 120px;
    border: 1px solid rgb(6 182 212 / 12%);
    border-radius: 999px;
}

.service-pillar:hover {
    border-color: rgb(6 182 212 / 30%);
    box-shadow: 0 24px 60px rgb(0 0 0 / 30%), 0 0 30px rgb(6 182 212 / 8%);
    transform: translateY(-8px);
}

.pillar-topline {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.pillar-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: rgb(6 182 212 / 10%);
    color: #22d3ee;
    border: 1px solid rgb(6 182 212 / 20%);
}

.pillar-svg {
    width: 24px;
    height: 24px;
}

.pillar-topline span {
    color: #22d3ee;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}

.service-pillar h3 {
    margin-bottom: 14px;
    font-size: 23px;
    line-height: 1.25;
    color: #e2e8f0;
}

.service-pillar p {
    margin-bottom: 22px;
    color: var(--text-light);
    font-size: 15px;
}

.service-pillar ul,
.pricing-card ul {
    display: grid;
    gap: 12px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.service-pillar li,
.pricing-card li {
    display: flex;
    align-items: center;
    gap: 9px;
    color: #cbd5e1;
    font-size: 14px;
    font-weight: 700;
}

.check-icon {
    width: 17px;
    height: 17px;
    flex-shrink: 0;
    color: #22d3ee;
}

.operations-panel {
    display: grid;
    grid-template-columns: 0.8fr 1.2fr;
    gap: 28px;
    align-items: center;
    margin-top: 32px;
    border: 1px solid rgb(6 182 212 / 12%);
    border-radius: 8px;
    background: rgb(5 8 16 / 90%);
    box-shadow: 0 24px 70px rgb(0 0 0 / 30%), 0 0 40px rgb(6 182 212 / 6%);
    padding: 32px;
}

.operations-copy span {
    color: #22d3ee;
    font-size: 13px;
    font-weight: 900;
    letter-spacing: 1.4px;
    text-transform: uppercase;
}

.operations-copy h3 {
    margin-top: 10px;
    color: #ffffff;
    font-size: 34px;
    line-height: 1.15;
}

.operations-copy p {
    margin-top: 16px;
    color: #cbd5e1;
}

.live-module-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}

.live-module {
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid rgb(255 255 255 / 6%);
    border-radius: 8px;
    background: rgb(255 255 255 / 4%);
    padding: 16px;
    transition: border-color 0.25s;
}

.live-module:hover {
    border-color: rgb(6 182 212 / 25%);
}

.live-module-icon {
    width: 24px;
    height: 24px;
    flex-shrink: 0;
    color: #22d3ee;
}

.live-module strong {
    display: block;
    color: #ffffff;
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 18px;
}

.live-module span {
    display: block;
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
}

.section {
    position: relative;
    padding: 120px 0;
}

.section-title {
    max-width: 700px;
    margin: 0 auto 80px;
    text-align: center;
}

.section-title span {
    display: block;
    margin-bottom: 10px;
    color: #22d3ee;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.section-title h2 {
    margin-bottom: 20px;
    color: #ffffff;
    font-size: 42px;
    font-weight: 700;
}

.section-title p {
    color: var(--text-light);
    font-size: 18px;
}

.business-solutions,
.portfolio {
    background: var(--dark-bg);
}

.solutions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 30px;
}

.solution-card {
    position: relative;
    display: flex;
    overflow: hidden;
    flex-direction: column;
    align-items: flex-start;
    border: 1px solid rgb(255 255 255 / 6%);
    border-radius: 8px;
    background: rgb(255 255 255 / 3%);
    padding: 40px 30px;
    transition:
        transform 0.4s ease,
        box-shadow 0.4s ease,
        border-color 0.4s ease;
}

.solution-card:hover {
    border-color: rgb(6 182 212 / 25%);
    box-shadow: 0 20px 40px rgb(0 0 0 / 30%), 0 0 30px rgb(6 182 212 / 6%);
    transform: translateY(-10px);
}

.solution-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: rgb(255 255 255 / 10%);
    transition: height 0.3s;
}

.solution-card:hover::before {
    height: 4px;
}

.solution-card.restaurante::before {
    background: var(--restaurant-color);
}

.solution-card.comercio::before {
    background: var(--retail-color);
}

.solution-card.farmacia::before {
    background: var(--pharmacy-color);
}

.solution-card.ferreteria::before {
    background: var(--hardware-color);
}

.solution-card.veterinaria::before {
    background: var(--vet-color);
}

.solution-card.saas::before {
    background: var(--saas-color);
}

.solution-card.chatbot::before {
    background: var(--chatbot-color);
}

.solution-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 70px;
    height: 70px;
    margin-bottom: 25px;
    border-radius: 8px;
    border: 1px solid rgb(255 255 255 / 8%);
    transition: transform 0.3s;
}

.restaurante .solution-icon-wrapper {
    background: rgb(244 63 94 / 10%);
    color: var(--restaurant-color);
}

.comercio .solution-icon-wrapper {
    background: rgb(16 185 129 / 10%);
    color: var(--retail-color);
}

.farmacia .solution-icon-wrapper {
    background: rgb(168 85 247 / 10%);
    color: var(--pharmacy-color);
}

.ferreteria .solution-icon-wrapper {
    background: rgb(245 158 11 / 10%);
    color: var(--hardware-color);
}

.veterinaria .solution-icon-wrapper {
    background: rgb(13 148 136 / 10%);
    color: var(--vet-color);
}

.saas .solution-icon-wrapper {
    background: rgb(6 182 212 / 10%);
    color: var(--saas-color);
}

.chatbot .solution-icon-wrapper {
    background: rgb(34 211 238 / 10%);
    color: var(--chatbot-color);
}

.solution-card:hover .solution-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.card-icon {
    width: 30px;
    height: 30px;
}

.solution-card h3 {
    margin-bottom: 15px;
    font-size: 24px;
    font-weight: 700;
    color: #e2e8f0;
}

.solution-card p {
    flex-grow: 1;
    margin-bottom: 25px;
    color: var(--text-light);
    font-size: 15px;
}

.features,
.contact {
    background-color: var(--light-bg);
    border-top: 1px solid rgb(255 255 255 / 4%);
}

.features-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    border-radius: 8px;
    border: 1px solid rgb(255 255 255 / 6%);
    background: rgb(255 255 255 / 3%);
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 20%);
    padding: 30px;
    transition: transform 0.3s, border-color 0.3s;
}

.feature-item:hover {
    transform: translateY(-5px);
    border-color: rgb(6 182 212 / 20%);
}

.feature-icon {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    margin-right: 25px;
    border-radius: 8px;
    background: linear-gradient(135deg, #06b6d4, #0e7490);
    box-shadow: 0 8px 20px rgb(6 182 212 / 30%);
    color: #ffffff;
}

.feature-svg {
    width: 24px;
    height: 24px;
}

.feature-text h3 {
    margin-bottom: 10px;
    color: #e2e8f0;
    font-size: 20px;
}

.feature-text p {
    color: var(--text-light);
    font-size: 15px;
}

.business-type-selector {
    margin-bottom: 50px;
}

.business-type-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    max-width: 100%;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid rgb(255 255 255 / 8%);
    border-radius: 999px;
    background: rgb(255 255 255 / 4%);
    color: #94a3b8;
    cursor: pointer;
    font-weight: 600;
    padding: 10px 25px;
    transition:
        transform 0.3s,
        box-shadow 0.3s,
        background 0.3s,
        color 0.3s,
        border-color 0.3s;
}

.filter-btn:hover,
.filter-btn.active {
    border-color: #06b6d4;
    background: linear-gradient(135deg, #06b6d4, #0e7490);
    box-shadow: 0 4px 16px rgb(6 182 212 / 25%);
    color: #ffffff;
    transform: translateY(-2px);
}

.portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
}

.portfolio-item {
    overflow: hidden;
    border: 1px solid rgb(255 255 255 / 6%);
    border-radius: 8px;
    background: rgb(255 255 255 / 3%);
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 30%);
    transition:
        transform 0.3s,
        box-shadow 0.3s,
        border-color 0.3s;
}

.portfolio-item:hover {
    border-color: rgb(6 182 212 / 20%);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 40%), 0 0 20px rgb(6 182 212 / 6%);
    transform: translateY(-8px);
}

.portfolio-img {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.portfolio-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.portfolio-img::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgb(0 0 0 / 70%), transparent);
    opacity: 0;
    transition: opacity 0.3s;
}

.portfolio-item:hover .portfolio-img img {
    transform: scale(1.1);
}

.portfolio-item:hover .portfolio-img::after {
    opacity: 1;
}

.portfolio-category {
    position: absolute;
    z-index: 1;
    top: 15px;
    right: 15px;
    border-radius: 6px;
    box-shadow: 0 2px 5px rgb(0 0 0 / 10%);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 5px 10px;
    text-transform: uppercase;
}

.restaurant-badge,
.retail-badge,
.pharmacy-badge,
.hardware-badge,
.vet-badge {
    color: #ffffff;
}

.restaurant-badge {
    background: var(--restaurant-color);
}

.retail-badge {
    background: var(--retail-color);
}

.pharmacy-badge {
    background: var(--pharmacy-color);
}

.hardware-badge {
    background: var(--hardware-color);
}

.vet-badge {
    background: var(--vet-color);
}

.sport-badge {
    background: var(--sport-color);
}

.portfolio-info {
    padding: 25px;
}

.portfolio-info h3 {
    margin-bottom: 10px;
    font-size: 20px;
    font-weight: 700;
    color: #e2e8f0;
}

.portfolio-info p {
    margin-bottom: 15px;
    color: #94a3b8;
}

.portfolio-links {
    display: flex;
    gap: 16px;
    align-items: center;
}

.portfolio-links button {
    display: flex;
    align-items: center;
    gap: 6px;
    border: 0;
    background: transparent;
    color: #22d3ee;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 0;
    text-transform: uppercase;
}

.portfolio-link-live {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #a855f7;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-decoration: none;
    text-transform: uppercase;
    transition: color 0.2s;
}

.portfolio-link-live:hover {
    color: #c084fc;
}

.testimonials {
    background: var(--dark-bg);
    border-top: 1px solid rgb(255 255 255 / 4%);
    color: #ffffff;
}

.testimonials .section-title h2 {
    color: #ffffff;
}

.testimonials .section-title p {
    color: #94a3b8;
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.testimonial-card {
    position: relative;
    border: 1px solid rgb(6 182 212 / 10%);
    border-radius: 8px;
    background: rgb(255 255 255 / 3%);
    padding: 40px 30px;
    backdrop-filter: blur(5px);
    transition: border-color 0.3s;
}

.testimonial-card:hover {
    border-color: rgb(6 182 212 / 25%);
}

.testimonial-card::before {
    content: '"';
    position: absolute;
    top: -20px;
    left: 20px;
    color: #06b6d4;
    font-family: Georgia, serif;
    font-size: 120px;
    line-height: 1;
    opacity: 0.15;
}

.testimonial-content {
    position: relative;
    margin-bottom: 20px;
    color: #e2e8f0;
    font-style: italic;
}

.testimonial-author {
    display: flex;
    align-items: center;
}

.testimonial-author img {
    width: 50px;
    height: 50px;
    margin-right: 15px;
    border-radius: 999px;
    object-fit: cover;
    border: 2px solid rgb(6 182 212 / 30%);
}

.author-info h4 {
    margin-bottom: 5px;
    color: #ffffff;
}

.author-info p {
    color: #22d3ee;
    font-size: 13px;
}

.pricing {
    background: var(--light-bg);
    border-top: 1px solid rgb(255 255 255 / 4%);
}

.pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
}

.pricing-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 26px;
    border: 1px solid rgb(255 255 255 / 8%);
    border-radius: 8px;
    background: rgb(255 255 255 / 3%);
    box-shadow: 0 12px 35px rgb(0 0 0 / 20%);
    padding: 30px;
    transition:
        border-color 0.3s ease,
        box-shadow 0.3s ease,
        transform 0.3s ease;
}

.pricing-card.highlighted {
    border-color: rgb(6 182 212 / 40%);
    box-shadow: 0 24px 70px rgb(6 182 212 / 12%), 0 0 40px rgb(6 182 212 / 8%);
    background: rgb(6 182 212 / 6%);
    transform: translateY(-10px);
}

.pricing-card:hover {
    border-color: rgb(6 182 212 / 30%);
    box-shadow: 0 24px 60px rgb(0 0 0 / 30%);
    transform: translateY(-8px);
}

.pricing-card.highlighted:hover {
    transform: translateY(-14px);
}

.popular-badge {
    display: inline-flex;
    width: fit-content;
    margin-bottom: 14px;
    border-radius: 999px;
    background: rgb(6 182 212 / 15%);
    border: 1px solid rgb(6 182 212 / 30%);
    color: #22d3ee;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: 1px;
    padding: 6px 10px;
    text-transform: uppercase;
}

.pricing-header h3 {
    font-size: 26px;
    color: #e2e8f0;
}

.pricing-header strong {
    display: block;
    margin-top: 12px;
    color: #ffffff;
    font-family: Poppins, Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 30px;
    line-height: 1.1;
}

.pricing-header p {
    margin-top: 14px;
    color: var(--text-light);
}

.pricing-card .btn {
    margin-top: auto;
}

.contact-form {
    max-width: 700px;
    margin: 0 auto;
    border: 1px solid rgb(6 182 212 / 12%);
    border-radius: 8px;
    background: rgb(255 255 255 / 3%);
    box-shadow: 0 20px 40px rgb(0 0 0 / 20%);
    padding: 40px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #cbd5e1;
    font-size: 14px;
    font-weight: 600;
}

.form-control {
    width: 100%;
    border: 1px solid rgb(255 255 255 / 10%);
    border-radius: 8px;
    background: rgb(255 255 255 / 5%);
    color: #e2e8f0;
    font-family: Inter, ui-sans-serif, system-ui, sans-serif;
    font-size: 16px;
    padding: 12px 15px;
    transition:
        border-color 0.3s,
        background 0.3s,
        box-shadow 0.3s;
}

.form-control:focus {
    border-color: #06b6d4;
    background: rgb(6 182 212 / 8%);
    box-shadow: 0 0 0 3px rgb(6 182 212 / 12%);
    outline: none;
}

.form-control option {
    background: #111827;
    color: #e2e8f0;
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

.form-error {
    margin-top: 6px;
    color: #f87171;
    font-size: 13px;
}

.submit-button {
    width: 100%;
}

.login-modal-backdrop {
    position: fixed;
    z-index: 2000;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background:
        radial-gradient(circle at 50% 10%, rgb(6 182 212 / 15%), transparent 34%),
        rgb(2 6 23 / 80%);
    padding: 24px;
    backdrop-filter: blur(12px);
}

.login-modal {
    position: relative;
    width: min(100%, 460px);
    max-height: calc(100vh - 48px);
    overflow-x: hidden;
    overflow-y: auto;
    border: 1px solid rgb(6 182 212 / 20%);
    border-radius: 8px;
    background: #0d1117;
    box-shadow: 0 30px 90px rgb(0 0 0 / 60%), 0 0 40px rgb(6 182 212 / 8%);
    padding: 34px;
}

.login-modal::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #06b6d4, #a855f7, #06b6d4);
    box-shadow: 0 0 12px rgb(6 182 212 / 50%);
}

.modal-close {
    position: absolute;
    top: 18px;
    right: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: 1px solid rgb(255 255 255 / 10%);
    border-radius: 8px;
    background: rgb(255 255 255 / 5%);
    color: #64748b;
    cursor: pointer;
    transition:
        background 0.25s ease,
        color 0.25s ease,
        transform 0.25s ease;
}

.modal-close:hover {
    background: rgb(255 255 255 / 10%);
    color: #e2e8f0;
    transform: translateY(-1px);
}

.login-modal-header {
    display: flex;
    gap: 16px;
    align-items: center;
    margin-bottom: 28px;
    padding-right: 44px;
}

.login-modal-icon {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 54px;
    height: 54px;
    border-radius: 8px;
    background: rgb(6 182 212 / 10%);
    color: #22d3ee;
    border: 1px solid rgb(6 182 212 / 20%);
}

.modal-icon-svg {
    width: 26px;
    height: 26px;
}

.login-modal-header p {
    color: #22d3ee;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.login-modal-header h2 {
    margin-top: 4px;
    color: #ffffff;
    font-size: 30px;
    line-height: 1.15;
}

.login-modal-form {
    display: grid;
    gap: 18px;
}

.login-label-row,
.remember-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.forgot-link {
    color: #22d3ee;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
}

.forgot-link:hover {
    color: #06b6d4;
    text-decoration: underline;
}

.remember-row {
    justify-content: flex-start;
    color: #94a3b8;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
}

.remember-row input {
    width: 18px;
    height: 18px;
    accent-color: #06b6d4;
}

.whatsapp-float {
    position: fixed;
    z-index: 1500;
    right: 24px;
    bottom: 24px;
    display: inline-flex;
    align-items: center;
    gap: 9px;
    min-height: 46px;
    border: 1px solid rgb(255 255 255 / 28%);
    border-radius: 999px;
    background: linear-gradient(135deg, #25d366, #128c7e);
    box-shadow:
        0 16px 36px rgb(18 140 126 / 32%),
        inset 0 1px 0 rgb(255 255 255 / 28%);
    color: #ffffff;
    font-size: 14px;
    font-weight: 900;
    padding: 11px 18px;
    text-decoration: none;
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease;
}

.whatsapp-float::before {
    content: '';
    position: absolute;
    inset: -7px;
    z-index: -1;
    border-radius: inherit;
    background: rgb(37 211 102 / 18%);
    animation: whatsappPulse 2.4s ease-in-out infinite;
}

.whatsapp-float:hover {
    box-shadow:
        0 20px 46px rgb(18 140 126 / 42%),
        inset 0 1px 0 rgb(255 255 255 / 28%);
    transform: translateY(-3px);
}

.whatsapp-float-icon {
    width: 20px;
    height: 20px;
}

@keyframes whatsappPulse {
    0%,
    100% {
        opacity: 0.55;
        transform: scale(0.92);
    }

    50% {
        opacity: 0;
        transform: scale(1.18);
    }
}

footer {
    border-top: 1px solid rgb(6 182 212 / 12%);
    background: #050810;
    color: #ffffff;
    padding: 60px 0 30px;
    text-align: center;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
}

.social-links button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border: 1px solid rgb(255 255 255 / 8%);
    border-radius: 999px;
    background: rgb(255 255 255 / 4%);
    color: #64748b;
    cursor: pointer;
    transition:
        transform 0.3s,
        background 0.3s,
        color 0.3s,
        border-color 0.3s;
}

.social-links button:hover {
    background: linear-gradient(135deg, #06b6d4, #0e7490);
    border-color: #06b6d4;
    color: #ffffff;
    transform: translateY(-5px);
}

.footer-note {
    margin-top: 10px;
    font-size: 14px;
    opacity: 0.5;
    color: #94a3b8;
}

.reveal {
    opacity: 0;
    transform: translateY(24px);
    transition:
        opacity 0.75s ease,
        transform 0.75s ease;
}

.reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.delay-1 {
    transition-delay: 0.12s;
}

.delay-2 {
    transition-delay: 0.24s;
}

.delay-3 {
    transition-delay: 0.36s;
}

@media (prefers-reduced-motion: reduce) {
    .landing-page *,
    .landing-page *::before,
    .landing-page *::after {
        scroll-behavior: auto;
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
    }
}

@media (max-width: 1100px) {
    .nav-links,
    .nav-actions {
        display: none;
    }

    .mobile-actions {
        display: flex;
    }

    .mobile-menu-button {
        display: flex;
    }

    .mobile-menu {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .mobile-menu button {
        width: 100%;
        justify-content: center;
        padding: 10px;
    }

    .hero-container {
        grid-template-columns: 1fr;
        gap: 50px;
        text-align: center;
    }

    .hero-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .hero-text p {
        max-width: 720px;
    }

    .hero-tech-row,
    .hero-btns {
        justify-content: center;
    }

    .hero-stats {
        width: 100%;
    }

    .hero-visual,
    .visual-card,
    .visual-card:hover {
        transform: none;
    }
}

@media (max-width: 900px) {
    .hero-text h1 {
        font-size: 42px;
    }

    .proof-strip {
        margin-top: 0;
        padding-top: 28px;
    }

    .px {
        display: none;
    }

    .proof-grid {
        grid-template-columns: 1fr;
    }

    .service-pillar-grid,
    .pricing-grid {
        grid-template-columns: 1fr;
    }

    .operations-panel {
        grid-template-columns: 1fr;
    }

    .live-module-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .pricing-card.highlighted {
        transform: none;
    }

    .impact-panel {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .impact-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .impact-whatsapp {
        justify-self: center;
    }
}

@media (max-width: 640px) {
    .container,
    .hero-container {
        width: min(100% - 32px, 1280px);
    }

    .section {
        padding: 84px 0;
    }

    .mobile-actions .login-cta {
        display: none;
    }

    .code-symbol-overlay {
        font-size: 52px;
    }

    .code-lines-left,
    .code-lines-right {
        display: none;
    }

    .section-title {
        margin-bottom: 52px;
    }

    .section-title h2 {
        font-size: 32px;
    }

    .section-title p,
    .hero-text p {
        font-size: 16px;
    }

    .hero {
        padding-top: 104px;
    }

    .hero-text h1 {
        font-size: 38px;
    }

    .hero-btns,
    .hero-btns .btn,
    .hero-btns .btn-outline {
        width: 100%;
    }

    .hero-stats {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .dashboard-preview {
        position: relative;
        right: auto;
        bottom: auto;
        width: 100%;
        margin-top: 12px;
    }

    .hero-module-cloud {
        position: relative;
        right: auto;
        bottom: auto;
        left: auto;
        margin-top: 12px;
    }

    .preview-grid {
        grid-template-columns: 1fr;
    }

    .preview-panel.wide {
        grid-row: auto;
    }

    .floating-icon {
        width: 45px;
        height: 45px;
    }

    .floating-svg {
        width: 20px;
        height: 20px;
    }

    .icon-1 {
        right: 0;
    }

    .icon-2 {
        left: 0;
    }

    .icon-3 {
        right: -10px;
    }

    .features-list,
    .portfolio-grid {
        grid-template-columns: 1fr;
    }

    .feature-item {
        flex-direction: column;
        gap: 18px;
    }

    .feature-icon {
        margin-right: 0;
    }

    .contact-form {
        padding: 26px;
    }

    .whatsapp-float {
        right: 18px;
        bottom: 18px;
        min-height: 46px;
        padding: 12px;
    }

    .whatsapp-float span {
        display: none;
    }

    .testimonials-grid {
        grid-template-columns: 1fr;
    }

    .solutions-grid {
        grid-template-columns: 1fr;
    }

    .operations-panel {
        padding: 24px;
    }

    .operations-copy h3 {
        font-size: 22px;
    }

    .live-module-grid {
        grid-template-columns: 1fr;
    }

    .impact-band {
        padding-top: 28px;
    }

    .impact-panel {
        padding: 22px;
    }

    .impact-copy h2 {
        font-size: 24px;
    }
}

@media (min-width: 641px) and (max-width: 900px) {
    .proof-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .service-pillar-grid,
    .pricing-grid {
        grid-template-columns: 1fr;
    }

    .hero-text h1 {
        font-size: 48px;
    }

    .section {
        padding: 100px 0;
    }
}

@media (max-width: 480px) {
    nav {
        min-height: 72px;
    }

    .logo-button {
        flex-shrink: 1;
    }

    .brand-logo {
        width: 52px;
        height: 52px;
        border-radius: 10px;
    }

    .hero {
        padding-top: 100px;
    }

    .hero-badge,
    .hero-tech-badge {
        justify-content: center;
        text-align: center;
    }

    .hero-text h1 {
        font-size: 30px;
    }

    .hero-stats {
        grid-template-columns: 1fr;
    }

    .section {
        padding: 60px 0;
    }

    .section-title {
        margin-bottom: 40px;
    }

    .section-title h2 {
        font-size: 26px;
    }

    .contact-form {
        padding: 20px 16px;
    }

    .testimonial-card {
        padding: 30px 20px;
    }

    .filter-btn {
        padding: 8px 16px;
        font-size: 13px;
    }

    .solution-card {
        padding: 28px 20px;
    }

    footer {
        padding: 40px 0 24px;
    }

    .proof-item {
        gap: 12px;
        padding: 18px;
    }

    .impact-stats {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 360px) {
    .container,
    .hero-container {
        width: min(100% - 24px, 1280px);
    }

    .hero-text h1 {
        font-size: 28px;
    }

    .btn,
    .btn-outline,
    .login-link {
        padding-right: 16px;
        padding-left: 16px;
    }

    .hero-stat,
    .service-pillar,
    .pricing-card,
    .feature-item,
    .portfolio-info {
        padding-right: 16px;
        padding-left: 16px;
    }

    .login-modal {
        padding: 28px 18px;
    }
}
</style>
