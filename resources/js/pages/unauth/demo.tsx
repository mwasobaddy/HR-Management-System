import { Head, Link, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { useState } from 'react';

interface DemoAccount {
    email: string;
    password: string;
    role: string;
    description: string;
}

interface DemoProps {
    demoAccounts: DemoAccount[];
}

export default function Demo({ demoAccounts }: DemoProps) {
    const [copiedEmail, setCopiedEmail] = useState<string | null>(null);

    const copyToClipboard = (text: string, email: string) => {
        navigator.clipboard.writeText(text);
        setCopiedEmail(email);
        setTimeout(() => setCopiedEmail(null), 2000);
    };

    const handleLoginAsDemo = (email: string, password: string) => {
        router.post('/login', {
            email,
            password,
        });
    };

    return (
        <>
            <Head title="Try Demo - Obseque HRMS" />

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
                                <Link href="/billing">
                                    <Button>Get Started</Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Main Content */}
                <div className="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <h1 className="text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Try the Demo
                        </h1>
                        <p className="mt-4 text-lg text-slate-600 dark:text-slate-400">
                            Experience Obseque HRMS with full Pro-level features. Choose any account below to log in.
                        </p>
                    </div>

                    {/* Demo Accounts */}
                    <div className="mt-12 space-y-4">
                        {demoAccounts.map((account, index) => (
                            <div
                                key={index}
                                className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
                            >
                                <div className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                    <div className="flex-1">
                                        <div className="flex items-center gap-3">
                                            <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-2xl dark:bg-blue-950">
                                                {account.role === 'Admin' && '👑'}
                                                {account.role === 'HR Manager' && '📋'}
                                                {account.role === 'Department Manager' && '👔'}
                                                {account.role === 'Employee' && '👤'}
                                            </div>
                                            <div>
                                                <h3 className="font-semibold text-slate-900 dark:text-white">
                                                    {account.role}
                                                </h3>
                                                <p className="text-sm text-slate-600 dark:text-slate-400">
                                                    {account.description}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div className="mt-4 space-y-2">
                                            <div className="flex items-center gap-2">
                                                <span className="text-sm font-medium text-slate-600 dark:text-slate-400">
                                                    Email:
                                                </span>
                                                <code className="rounded bg-slate-100 px-2 py-1 text-sm text-slate-900 dark:bg-slate-800 dark:text-slate-100">
                                                    {account.email}
                                                </code>
                                                <button
                                                    onClick={() => copyToClipboard(account.email, account.email)}
                                                    className="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                                    title="Copy email"
                                                >
                                                    {copiedEmail === account.email ? (
                                                        <svg className="h-4 w-4 text-green-500" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    ) : (
                                                        <svg className="h-4 w-4" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                    )}
                                                </button>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <span className="text-sm font-medium text-slate-600 dark:text-slate-400">
                                                    Password:
                                                </span>
                                                <code className="rounded bg-slate-100 px-2 py-1 text-sm text-slate-900 dark:bg-slate-800 dark:text-slate-100">
                                                    {account.password}
                                                </code>
                                                <button
                                                    onClick={() => copyToClipboard(account.password, account.password)}
                                                    className="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                                    title="Copy password"
                                                >
                                                    {copiedEmail === account.password ? (
                                                        <svg className="h-4 w-4 text-green-500" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    ) : (
                                                        <svg className="h-4 w-4" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                        </svg>
                                                    )}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex flex-col gap-2">
                                        <Button
                                            onClick={() => handleLoginAsDemo(account.email, account.password)}
                                            className="w-full md:w-auto"
                                        >
                                            Login as {account.role}
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Info Section */}
                    <div className="mt-12 rounded-lg border border-blue-200 bg-blue-50 p-6 dark:border-blue-900 dark:bg-blue-950/20">
                        <div className="flex items-start gap-3">
                            <svg className="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h3 className="font-semibold text-slate-900 dark:text-white">
                                    About the Demo
                                </h3>
                                <ul className="mt-2 space-y-1 text-sm text-slate-600 dark:text-slate-400">
                                    <li>• All Pro-level features are enabled in the demo</li>
                                    <li>• Demo data includes sample employees, departments, and records</li>
                                    <li>• You can reset demo data at any time using the "Reset Demo" button in settings</li>
                                    <li>• Demo accounts are shared - changes may be visible to other demo users</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {/* CTA */}
                    <div className="mt-12 text-center">
                        <p className="text-slate-600 dark:text-slate-400">
                            Ready to get started with your own account?
                        </p>
                        <Link href="/billing" className="mt-4 inline-block">
                            <Button size="lg">Choose Your Plan</Button>
                        </Link>
                    </div>
                </div>
            </div>
        </>
    );
}
