import { CheckCircle2, LucideIcon } from 'lucide-react';

interface Step {
    number: number;
    title: string;
    description: string;
    icon: LucideIcon;
}

interface OnboardingStepIndicatorProps {
    steps: Step[];
    currentStep: number;
}

export default function OnboardingStepIndicator({ steps, currentStep }: OnboardingStepIndicatorProps) {
    return (
        <div className="mb-8">
            <div className="flex flex-col max-w-3xl mx-auto">
                {steps.map((step, index) => {
                    const Icon = step.icon;
                    const isActive = currentStep === step.number;
                    const isCompleted = currentStep > step.number;
                    // const isUpcoming = currentStep < step.number;

                    return (
                        <div key={step.number} className="flex gap-2">
                            {/* Left side - Icon and connector line */}
                            <div className="flex flex-col items-center">
                                <div 
                                    className={` ${
                                        isCompleted ? 'bg-red-200 dark:bg-red-700/25 p-2 rounded-full transition-all' : 'px-2'
                                    }`}
                                >
                                    <div
                                        className={`w-12 h-12 rounded-full flex items-center justify-center transition-all ${
                                            isActive
                                                ? 'bg-white text-red-600 shadow-lg border-2 border-red-400'
                                                : isCompleted
                                                ? 'bg-red-600 text-white'
                                                : 'bg-white text-gray-500 border-2 border-gray-300'
                                        }`}
                                    >
                                        {isCompleted ? (
                                            <CheckCircle2 className="h-7 w-7" />
                                        ) : (
                                            <Icon className="h-7 w-7" />
                                        )}
                                    </div>
                                </div>
                                
                                {/* Vertical connector line */}
                                {index < steps.length - 1 && (
                                    <div className="w-0.5 flex-1">
                                        <div 
                                            className={`w-full h-full transition-all ${
                                                isCompleted ? 'bg-red-600' : 'bg-gray-300 border-dashed border-l-2 border-gray-400'
                                            }`}
                                        />
                                    </div>
                                )}
                            </div>

                            {/* Right side - Content */}
                            <div className="flex-1 pb-16">
                                <h3 className={`text-xl font-bold mb-3`}>
                                    {step.title}
                                </h3>
                                <p className={`text-muted-foreground`}>
                                    {step.description}
                                </p>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}