import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

interface SubscriptionPlan {
    id: number;
    name: string;
    slug: string;
    description: string;
    price_monthly: number;
    price_yearly: number;
    max_users: number;
    max_job_posts: number;
    features: string[];
}

interface HomeProps {
    plans: SubscriptionPlan[];
}

export default function Home({ plans }: HomeProps) {
    return (
        <>
            <Head title="Obseque HRMS - Modern HR Management System" />

            <div className="min-h-screen bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900">
                {/* Navigation */}
                <nav className="fixed top-0 left-0 right-0 z-50 border-b border-slate-200 bg-white/80 backdrop-blur-sm dark:border-slate-800 dark:bg-slate-950/80">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 items-center justify-between">
                            <div className="flex items-center">
                                <Link href="/" className="text-2xl font-bold text-slate-900 dark:text-white">
                                    Obseque <span className="text-blue-600">HRMS</span>
                                </Link>
                            </div>
                            <div className="hidden md:flex md:items-center md:space-x-8">
                                <a href="#features" className="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                                    Features
                                </a>
                                <a href="#pricing" className="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                                    Pricing
                                </a>
                                <Link href="/support" className="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                                    Support
                                </Link>
                                <Link href="/demo" className="text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                                    Try Demo
                                </Link>
                            </div>
                            <div className="flex items-center space-x-4">
                                <Link href="/login">
                                    <Button variant="ghost">Log in</Button>
                                </Link>
                                <Link href="/billing">
                                    <Button>Get Started</Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Hero Section */}
                <section className="relative overflow-hidden pt-32 pb-20">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="text-center">
                            <h1 className="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl md:text-6xl dark:text-white">
                                Modern HR Management
                                <span className="block text-blue-600">Made Simple</span>
                            </h1>
                            <p className="mx-auto mt-6 max-w-2xl text-lg text-slate-600 dark:text-slate-400">
                                Streamline your HR operations with Obseque HRMS. From employee management to payroll processing, 
                                everything you need in one powerful platform.
                            </p>
                            <div className="mt-10 flex items-center justify-center gap-4">
                                <Link href="/billing">
                                    <Button size="lg" className="text-lg">
                                        Start Free Trial
                                    </Button>
                                </Link>
                                <Link href="/demo">
                                    <Button size="lg" variant="outline" className="text-lg">
                                        Try Demo
                                    </Button>
                                </Link>
                            </div>
                            <p className="mt-4 text-sm text-slate-500">
                                No credit card required • 14-day free trial • Cancel anytime
                            </p>
                        </div>
                    </div>

                    {/* Hero Image/Illustration Placeholder */}
                    <div className="mx-auto mt-16 max-w-5xl px-4">
                        <div className="rounded-xl border border-slate-200 bg-slate-100 p-8 shadow-2xl dark:border-slate-800 dark:bg-slate-900">
                            <div className="aspect-video rounded-lg bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-slate-800 dark:to-slate-700" />
                        </div>
                    </div>
                </section>

                {/* Features Section */}
                <section id="features" className="py-20 bg-white dark:bg-slate-950">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="text-center">
                            <h2 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl dark:text-white">
                                Everything you need to manage your workforce
                            </h2>
                            <p className="mt-4 text-lg text-slate-600 dark:text-slate-400">
                                Powerful features designed to simplify HR management
                            </p>
                        </div>

                        <div className="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                            {[
                                {
                                    title: 'Employee Management',
                                    description: 'Complete employee records, documents, and profiles in one place',
                                    icon: '👥',
                                },
                                {
                                    title: 'Attendance Tracking',
                                    description: 'Clock in/out, leave management, and attendance reports',
                                    icon: '⏰',
                                },
                                {
                                    title: 'Payroll Processing',
                                    description: 'Automated salary calculations, tax deductions, and payslips',
                                    icon: '💰',
                                },
                                {
                                    title: 'Recruitment',
                                    description: 'Job postings, applicant tracking, and AI-powered CV screening',
                                    icon: '🎯',
                                },
                                {
                                    title: 'Performance Management',
                                    description: 'Reviews, goals, KPIs, and performance tracking',
                                    icon: '📊',
                                },
                                {
                                    title: 'Reports & Analytics',
                                    description: 'Comprehensive HR metrics and compliance reports',
                                    icon: '📈',
                                },
                            ].map((feature, index) => (
                                <div
                                    key={index}
                                    className="rounded-lg border border-slate-200 bg-white p-6 transition-shadow hover:shadow-lg dark:border-slate-800 dark:bg-slate-900"
                                >
                                    <div className="text-4xl mb-4">{feature.icon}</div>
                                    <h3 className="text-lg font-semibold text-slate-900 dark:text-white">
                                        {feature.title}
                                    </h3>
                                    <p className="mt-2 text-slate-600 dark:text-slate-400">
                                        {feature.description}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Pricing Section */}
                <section id="pricing" className="py-20 bg-slate-50 dark:bg-slate-900">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="text-center">
                            <h2 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl dark:text-white">
                                Simple, transparent pricing
                            </h2>
                            <p className="mt-4 text-lg text-slate-600 dark:text-slate-400">
                                Choose the plan that fits your organization
                            </p>
                        </div>

                        <div className="mt-16 grid gap-8 lg:grid-cols-4">
                            {plans.map((plan) => (
                                <div
                                    key={plan.id}
                                    className={`rounded-lg border p-8 ${
                                        plan.slug === 'pro'
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/20 shadow-lg scale-105'
                                            : 'border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900'
                                    }`}
                                >
                                    {plan.slug === 'pro' && (
                                        <div className="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2">
                                            <span className="inline-flex rounded-full bg-blue-600 px-4 py-1 text-xs font-semibold text-white">
                                                Most Popular
                                            </span>
                                        </div>
                                    )}
                                    <h3 className="text-xl font-semibold text-slate-900 dark:text-white">
                                        {plan.name}
                                    </h3>
                                    <p className="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                        {plan.description}
                                    </p>
                                    <div className="mt-6">
                                        <span className="text-4xl font-bold text-slate-900 dark:text-white">
                                            {plan.price_monthly === 0
                                                ? 'Free'
                                                : `$${plan.price_monthly}`}
                                        </span>
                                        {plan.price_monthly > 0 && (
                                            <span className="text-slate-600 dark:text-slate-400">/month</span>
                                        )}
                                    </div>
                                    <ul className="mt-6 space-y-3">
                                        {plan.features.map((feature, idx) => (
                                            <li key={idx} className="flex items-start text-sm">
                                                <span className="mr-2">✓</span>
                                                <span className="text-slate-600 dark:text-slate-400">{feature}</span>
                                            </li>
                                        ))}
                                    </ul>
                                    <Link href="/billing" className="mt-8 block">
                                        <Button
                                            className="w-full"
                                            variant={plan.slug === 'pro' ? 'default' : 'outline'}
                                        >
                                            {plan.slug === 'free' ? 'Start Free' : 'Get Started'}
                                        </Button>
                                    </Link>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* CTA Section */}
                <section className="py-20 bg-blue-600">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
                        <h2 className="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                            Ready to transform your HR management?
                        </h2>
                        <p className="mt-4 text-lg text-blue-100">
                            Join thousands of companies using Obseque HRMS
                        </p>
                        <div className="mt-10 flex items-center justify-center gap-4">
                            <Link href="/billing">
                                <Button size="lg" variant="secondary" className="text-lg">
                                    Start Free Trial
                                </Button>
                            </Link>
                            <Link href="/demo">
                                <Button size="lg" variant="outline" className="text-lg border-white text-white hover:bg-blue-700">
                                    Try Demo
                                </Button>
                            </Link>
                        </div>
                    </div>
                </section>

                {/* Footer */}
                <footer className="border-t border-slate-200 bg-white py-12 dark:border-slate-800 dark:bg-slate-950">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="grid gap-8 md:grid-cols-4">
                            <div>
                                <h3 className="text-lg font-semibold text-slate-900 dark:text-white">
                                    Obseque HRMS
                                </h3>
                                <p className="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                    Modern HR management for growing businesses
                                </p>
                            </div>
                            <div>
                                <h4 className="text-sm font-semibold text-slate-900 dark:text-white">Product</h4>
                                <ul className="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                    <li><a href="#features">Features</a></li>
                                    <li><a href="#pricing">Pricing</a></li>
                                    <li><Link href="/demo">Demo</Link></li>
                                </ul>
                            </div>
                            <div>
                                <h4 className="text-sm font-semibold text-slate-900 dark:text-white">Company</h4>
                                <ul className="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                    <li><a href="https://obseque.com">About Obseque</a></li>
                                    <li><Link href="/support">Support</Link></li>
                                    <li><a href="#">Contact</a></li>
                                </ul>
                            </div>
                            <div>
                                <h4 className="text-sm font-semibold text-slate-900 dark:text-white">Legal</h4>
                                <ul className="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                                    <li><a href="#">Privacy</a></li>
                                    <li><a href="#">Terms</a></li>
                                </ul>
                            </div>
                        </div>
                        <div className="mt-8 border-t border-slate-200 pt-8 text-center text-sm text-slate-600 dark:border-slate-800 dark:text-slate-400">
                            © {new Date().getFullYear()} Obseque. All rights reserved.
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
