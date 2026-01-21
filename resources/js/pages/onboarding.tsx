import { Head, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { CheckCircle } from 'lucide-react';

export default function Onboarding() {
    const completeOnboarding = () => {
        router.post('/onboarding/complete');
    };

    return (
        <>
            <Head title="Welcome!" />
            
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
                <Card className="w-full max-w-2xl">
                    <CardHeader className="text-center">
                        <div className="flex justify-center mb-4">
                            <div className="h-16 w-16 bg-green-600 rounded-full flex items-center justify-center">
                                <CheckCircle className="h-10 w-10 text-white" />
                            </div>
                        </div>
                        <CardTitle className="text-3xl font-bold">Welcome to Your HR Management System!</CardTitle>
                        <CardDescription className="text-lg mt-2">
                            Your account has been created successfully. Let's get started!
                        </CardDescription>
                    </CardHeader>
                    
                    <CardContent className="space-y-6">
                        <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 className="font-semibold text-blue-900 mb-2">🎉 Your 14-Day Free Trial Has Started</h3>
                            <p className="text-sm text-blue-800">
                                You have full access to all Free tier features. No credit card required.
                            </p>
                        </div>

                        <div className="space-y-4">
                            <h3 className="font-semibold text-lg">What's Next?</h3>
                            
                            <div className="space-y-3">
                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">
                                        1
                                    </div>
                                    <div>
                                        <h4 className="font-medium">Explore the Dashboard</h4>
                                        <p className="text-sm text-gray-600">
                                            Familiarize yourself with the main dashboard and features.
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">
                                        2
                                    </div>
                                    <div>
                                        <h4 className="font-medium">Add Your First Employee</h4>
                                        <p className="text-sm text-gray-600">
                                            Start building your employee database.
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">
                                        3
                                    </div>
                                    <div>
                                        <h4 className="font-medium">Create Departments</h4>
                                        <p className="text-sm text-gray-600">
                                            Organize your company structure with departments.
                                        </p>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3">
                                    <div className="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">
                                        4
                                    </div>
                                    <div>
                                        <h4 className="font-medium">Configure Company Settings</h4>
                                        <p className="text-sm text-gray-600">
                                            Set up your work hours, holidays, and other preferences.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p className="text-sm text-gray-700">
                                <span className="font-semibold">Need help?</span> Visit our{' '}
                                <a href="/support" className="text-blue-600 hover:underline">
                                    support center
                                </a>{' '}
                                or check out the{' '}
                                <a href="/demo" className="text-blue-600 hover:underline">
                                    demo
                                </a>{' '}
                                to see all features in action.
                            </p>
                        </div>

                        <Button
                            onClick={completeOnboarding}
                            className="w-full"
                            size="lg"
                        >
                            Go to Dashboard
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
