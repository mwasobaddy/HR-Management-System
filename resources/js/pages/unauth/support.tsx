import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';

import { Button } from '@/components/ui/button';

export default function Support() {
    const [submitted, setSubmitted] = useState(false);
    
    const { data, setData, processing } = useForm({
        name: '',
        email: '',
        subject: '',
        message: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        // TODO: Implement contact form submission in Phase 2
        setSubmitted(true);
        setTimeout(() => setSubmitted(false), 3000);
    };

    const faqs = [
        {
            question: 'How do I get started?',
            answer: 'Simply visit our pricing page, choose a plan that fits your needs, and follow the setup wizard after registration.',
        },
        {
            question: 'What payment methods do you accept?',
            answer: 'We accept all major credit cards (Visa, Mastercard, American Express), PayPal, and M-Pesa for East African customers.',
        },
        {
            question: 'Can I change plans later?',
            answer: 'Yes! You can upgrade or downgrade your plan at any time. Changes will be reflected in your next pricing cycle.',
        },
        {
            question: 'Is my data secure?',
            answer: 'Absolutely. We use industry-standard encryption, regular security audits, and comply with GDPR regulations.',
        },
        {
            question: 'Do you offer custom solutions?',
            answer: 'Yes, our Enterprise plan offers custom features, white-label branding, and dedicated support.',
        },
        {
            question: 'What happens if I cancel?',
            answer: 'You can cancel anytime. Your data will be available for 30 days after cancellation for export.',
        },
    ];

    return (
        <>
            <Head title="Support - Obseque HRMS" />

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
                                <Link href="/pricing">
                                    <Button>Get Started</Button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>

                {/* Main Content */}
                <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <h1 className="text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
                            How can we help?
                        </h1>
                        <p className="mt-4 text-lg text-slate-600 dark:text-slate-400">
                            Get answers to your questions or reach out to our support team
                        </p>
                    </div>

                    <div className="mt-16 grid gap-12 lg:grid-cols-2">
                        {/* FAQ Section */}
                        <div>
                            <h2 className="text-2xl font-bold text-slate-900 dark:text-white">
                                Frequently Asked Questions
                            </h2>
                            <div className="mt-6 space-y-4">
                                {faqs.map((faq, index) => (
                                    <details
                                        key={index}
                                        className="group rounded-lg border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900"
                                    >
                                        <summary className="flex cursor-pointer items-center justify-between p-4 font-medium text-slate-900 dark:text-white">
                                            {faq.question}
                                            <svg
                                                className="h-5 w-5 transition-transform group-open:rotate-180"
                                                fill="none"
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth="2"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </summary>
                                        <div className="border-t border-slate-200 p-4 text-slate-600 dark:border-slate-800 dark:text-slate-400">
                                            {faq.answer}
                                        </div>
                                    </details>
                                ))}
                            </div>
                        </div>

                        {/* Contact Form */}
                        <div>
                            <h2 className="text-2xl font-bold text-slate-900 dark:text-white">
                                Contact Us
                            </h2>
                            <p className="mt-2 text-slate-600 dark:text-slate-400">
                                Can't find what you're looking for? Send us a message.
                            </p>

                            {submitted ? (
                                <div className="mt-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-950/20">
                                    <div className="flex items-center gap-3">
                                        <svg className="h-5 w-5 text-green-600" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                        <p className="text-green-800 dark:text-green-200">
                                            Message sent successfully! We'll get back to you soon.
                                        </p>
                                    </div>
                                </div>
                            ) : (
                                <form onSubmit={submit} className="mt-6 space-y-4">
                                    <div>
                                        <label htmlFor="name" className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            Name
                                        </label>
                                        <input
                                            id="name"
                                            type="text"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            className="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label htmlFor="email" className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            Email
                                        </label>
                                        <input
                                            id="email"
                                            type="email"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            className="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label htmlFor="subject" className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            Subject
                                        </label>
                                        <input
                                            id="subject"
                                            type="text"
                                            value={data.subject}
                                            onChange={(e) => setData('subject', e.target.value)}
                                            className="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label htmlFor="message" className="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            Message
                                        </label>
                                        <textarea
                                            id="message"
                                            rows={5}
                                            value={data.message}
                                            onChange={(e) => setData('message', e.target.value)}
                                            className="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                            required
                                        />
                                    </div>

                                    <Button type="submit" className="w-full" disabled={processing}>
                                        {processing ? 'Sending...' : 'Send Message'}
                                    </Button>
                                </form>
                            )}

                            {/* Contact Info */}
                            <div className="mt-8 space-y-4">
                                <div className="flex items-start gap-3">
                                    <svg className="mt-1 h-5 w-5 text-blue-600" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p className="font-medium text-slate-900 dark:text-white">Email</p>
                                        <a href="mailto:support@obseque.com" className="text-blue-600 hover:text-blue-700">
                                            support@obseque.com
                                        </a>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3">
                                    <svg className="mt-1 h-5 w-5 text-blue-600" fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p className="font-medium text-slate-900 dark:text-white">Support Hours</p>
                                        <p className="text-slate-600 dark:text-slate-400">
                                            Monday - Friday: 9 AM - 6 PM (EST)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Additional Resources */}
                    <div className="mt-16 grid gap-6 sm:grid-cols-3">
                        <Link href="/demo" className="rounded-lg border border-slate-200 bg-white p-6 transition-shadow hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                            <div className="text-3xl mb-3">🎮</div>
                            <h3 className="font-semibold text-slate-900 dark:text-white">
                                Try Demo
                            </h3>
                            <p className="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                Explore all features with our interactive demo
                            </p>
                        </Link>

                        <a href="#" className="rounded-lg border border-slate-200 bg-white p-6 transition-shadow hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                            <div className="text-3xl mb-3">📚</div>
                            <h3 className="font-semibold text-slate-900 dark:text-white">
                                Documentation
                            </h3>
                            <p className="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                Comprehensive guides and API docs
                            </p>
                        </a>

                        <a href="#" className="rounded-lg border border-slate-200 bg-white p-6 transition-shadow hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
                            <div className="text-3xl mb-3">💬</div>
                            <h3 className="font-semibold text-slate-900 dark:text-white">
                                Community
                            </h3>
                            <p className="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                Join our community forum
                            </p>
                        </a>
                    </div>
                </div>
            </div>
        </>
    );
}
