import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';

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
    has_onboarding_framework: boolean;
    has_ai_features: boolean;
    has_api_access: boolean;
    has_payroll: boolean;
    has_subdomain: boolean;
}

interface PricingProps {
    plans: SubscriptionPlan[];
}

export default function Pricing({ plans }: PricingProps) {
    const [pricingCycle, setPricingCycle] = useState<'monthly' | 'yearly'>('monthly');

    const handleSelectPlan = (planId: number) => {
        router.visit(`/subscribe?plan=${planId}`);
    };

    return (
        <>
            <Head title="Pricing - Choose Your Plan" />

            <div className="min-h-screen bg-linear-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900">
                {/* Navigation */}
                <nav className="border-b border-[#8E2DE2] dark:border-[#7209B7]/30 bg-white dark:bg-slate-950">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 items-center justify-between">
                            <Link href="/" className="text-2xl font-bold text-[#F00000]">
                                Obseque <span className="text-[#F00000]">HRMS</span>
                            </Link>
                            <div className="flex items-center space-x-4">
                                <Link href="/">
                                    <Button variant="ghost" className="text-[#F00000]">Back to Home</Button>
                                </Link>
                                <Link href="/login">
                                    <Button variant="ghost" className="text-[#F00000]">Log in</Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Main Content */}
                <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <h1 className="text-4xl font-bold tracking-tight text-[#F00000]">
                            Choose Your Plan
                        </h1>
                        <p className="mt-4 text-lg text-[#F00000]">
                            Select the plan that best fits your organization's needs
                        </p>

                        {/* Pricing Cycle Toggle */}
                        <div className="mt-8 inline-flex rounded-lg border border-[#8E2DE2] dark:border-[#7209B7]/30 p-1">
                            <button
                                onClick={() => setPricingCycle('monthly')}
                                className={`rounded-md px-4 py-2 text-sm font-medium transition-colors ${
                                    pricingCycle === 'monthly'
                                        ? 'bg-[#F00000] text-white'
                                        : 'text-[#F00000] hover:text-[#F00000] dark:text-[#F00000] dark:hover:text-[#F00000]'
                                }`}
                            >
                                Monthly
                            </button>
                            <button
                                onClick={() => setPricingCycle('yearly')}
                                className={`rounded-md px-4 py-2 text-sm font-medium transition-colors ${
                                    pricingCycle === 'yearly'
                                        ? 'bg-[#F00000] text-white'
                                        : 'text-[#F00000] hover:text-[#F00000] dark:text-[#F00000] dark:hover:text-[#F00000]'
                                }`}
                            >
                                Yearly
                                <span className="ml-1.5 text-xs">(Save 17%)</span>
                            </button>
                        </div>
                    </div>

                    {/* Plans Grid */}
                    <div className="mt-16 grid gap-8 lg:grid-cols-4">
                        {plans.map((plan) => {
                            const price = pricingCycle === 'monthly' ? plan.price_monthly : plan.price_yearly;
                            const isPopular = plan.slug === 'pro';

                            return (
                                <div
                                    key={plan.id}
                                    className={`relative rounded-xl border p-8 ${
                                        isPopular
                                            ? 'border-[#8E2DE2] bg-blue-50 shadow-xl scale-105 dark:bg-blue-950/20'
                                            : 'border-[#8E2DE2] bg-white dark:border-[#7209B7]/30 dark:bg-slate-900'
                                    }`}
                                >
                                    {isPopular && (
                                        <div className="absolute -top-4 left-1/2 -translate-x-1/2">
                                            <span className="inline-flex rounded-full bg-blue-600 px-4 py-1 text-xs font-semibold text-white">
                                                Most Popular
                                            </span>
                                        </div>
                                    )}

                                    <div>
                                        <h3 className="text-xl font-bold text-[#F00000]">
                                            {plan.name}
                                        </h3>
                                        <p className="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                            {plan.description}
                                        </p>
                                    </div>

                                    <div className="mt-6">
                                        {price === 0 ? (
                                            <div className="text-4xl font-bold text-slate-900 dark:text-white">
                                                Free
                                            </div>
                                        ) : (
                                            <div>
                                                <span className="text-4xl font-bold text-slate-900 dark:text-white">
                                                    ${pricingCycle === 'yearly' ? (price / 12).toFixed(2) : price}
                                                </span>
                                                <span className="text-slate-600 dark:text-slate-400">/month</span>
                                                {pricingCycle === 'yearly' && (
                                                    <div className="mt-1 text-sm text-slate-600 dark:text-slate-400">
                                                        ${price}/year
                                                    </div>
                                                )}
                                            </div>
                                        )}
                                    </div>

                                    <div className="mt-6 space-y-3 border-t border-slate-200 pt-6 dark:border-slate-800">
                                        <div className="flex items-center text-sm">
                                            <span className="font-medium text-slate-900 dark:text-white">
                                                {plan.max_users === -1 ? 'Unlimited' : plan.max_users} users
                                            </span>
                                        </div>
                                        <div className="flex items-center text-sm">
                                            <span className="font-medium text-slate-900 dark:text-white">
                                                {plan.max_job_posts === -1 ? 'Unlimited' : plan.max_job_posts} job posts
                                            </span>
                                        </div>
                                    </div>

                                    <ul className="mt-6 space-y-3">
                                        {plan.features.map((feature, idx) => (
                                            <li key={idx} className="flex items-start text-sm">
                                                <svg
                                                    className="mr-2 h-5 w-5 shrink-0 text-green-500"
                                                    fill="none"
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span className="text-slate-600 dark:text-slate-400">{feature}</span>
                                            </li>
                                        ))}
                                    </ul>

                                    <Button
                                        onClick={() => handleSelectPlan(plan.id)}
                                        className="mt-8 w-full"
                                        variant={isPopular ? 'default' : 'outline'}
                                    >
                                        {plan.slug === 'free' ? 'Start Free Trial' : 'Get Started'}
                                    </Button>
                                </div>
                            );
                        })}
                    </div>

                    {/* FAQ or Additional Info */}
                    <div className="mt-16 text-center">
                        <p className="text-slate-600 dark:text-slate-400">
                            Need a custom solution?{' '}
                            <Link href="/support" className="font-medium text-blue-600 hover:text-blue-700">
                                Contact us
                            </Link>{' '}
                            for Enterprise options.
                        </p>
                    </div>
                </div>
            </div>
        </>
    );
}
