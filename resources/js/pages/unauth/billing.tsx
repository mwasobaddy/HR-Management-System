import { Head, Link, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { useState } from 'react';

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

interface BillingProps {
    plans: SubscriptionPlan[];
}

export default function Billing({ plans }: BillingProps) {
    const [billingCycle, setBillingCycle] = useState<'monthly' | 'yearly'>('monthly');

    const handleSelectPlan = (planSlug: string) => {
        if (planSlug === 'free') {
            // Redirect to registration for free plan
            router.visit('/register');
        } else {
            // TODO: Implement payment processing
            alert('Payment processing will be implemented in Phase 2');
        }
    };

    return (
        <>
            <Head title="Billing - Choose Your Plan" />

            <div className="min-h-screen bg-gradient-to-b from-slate-50 to-white dark:from-slate-950 dark:to-slate-900">
                {/* Navigation */}
                <nav className="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 items-center justify-between">
                            <Link href="/" className="text-2xl font-bold text-slate-900 dark:text-white">
                                Obseque <span className="text-blue-600">HRMS</span>
                            </Link>
                            <div className="flex items-center space-x-4">
                                <Link href="/">
                                    <Button variant="ghost">Back to Home</Button>
                                </Link>
                                <Link href="/login">
                                    <Button variant="ghost">Log in</Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Main Content */}
                <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <h1 className="text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Choose Your Plan
                        </h1>
                        <p className="mt-4 text-lg text-slate-600 dark:text-slate-400">
                            Select the plan that best fits your organization's needs
                        </p>

                        {/* Billing Cycle Toggle */}
                        <div className="mt-8 inline-flex rounded-lg border border-slate-200 p-1 dark:border-slate-800">
                            <button
                                onClick={() => setBillingCycle('monthly')}
                                className={`rounded-md px-4 py-2 text-sm font-medium transition-colors ${
                                    billingCycle === 'monthly'
                                        ? 'bg-blue-600 text-white'
                                        : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
                                }`}
                            >
                                Monthly
                            </button>
                            <button
                                onClick={() => setBillingCycle('yearly')}
                                className={`rounded-md px-4 py-2 text-sm font-medium transition-colors ${
                                    billingCycle === 'yearly'
                                        ? 'bg-blue-600 text-white'
                                        : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'
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
                            const price = billingCycle === 'monthly' ? plan.price_monthly : plan.price_yearly;
                            const isPopular = plan.slug === 'pro';

                            return (
                                <div
                                    key={plan.id}
                                    className={`relative rounded-xl border p-8 ${
                                        isPopular
                                            ? 'border-blue-500 bg-blue-50 shadow-xl scale-105 dark:bg-blue-950/20'
                                            : 'border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900'
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
                                        <h3 className="text-xl font-bold text-slate-900 dark:text-white">
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
                                                    ${billingCycle === 'yearly' ? (price / 12).toFixed(2) : price}
                                                </span>
                                                <span className="text-slate-600 dark:text-slate-400">/month</span>
                                                {billingCycle === 'yearly' && (
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
                                                    className="mr-2 h-5 w-5 flex-shrink-0 text-green-500"
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
                                        onClick={() => handleSelectPlan(plan.slug)}
                                        className="mt-8 w-full"
                                        variant={isPopular ? 'default' : 'outline'}
                                    >
                                        {plan.slug === 'free' ? 'Start Free' : 'Select Plan'}
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
