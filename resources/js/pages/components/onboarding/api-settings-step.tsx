import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface APISettingsStepProps {
    formData: {
        ai_provider: string;
        ai_model: string;
        ai_api_key: string;
        google_calendar_api_key: string;
        google_meet_api_key: string;
        smtp_host: string;
        smtp_port: number;
        smtp_username: string;
        smtp_password: string;
        smtp_encryption: string;
        smtp_from_address: string;
        smtp_from_name: string;
    };
    onChange: (field: string, value: unknown) => void;
}

export default function APISettingsStep({ formData, onChange }: APISettingsStepProps) {
    return (
        <div className="space-y-6">
            <div className="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p className="text-sm text-blue-800">
                    These settings are optional but recommended for enhanced functionality. You can configure them now or
                    skip and set them up later in settings.
                </p>
            </div>

            {/* AI API Section */}
            <div className="space-y-4 border-b pb-6">
                <div className="flex items-center justify-between">
                    <h3 className="text-lg font-semibold">AI Integration</h3>
                    <a
                        href="https://www.youtube.com/results?search_query=how+to+get+openai+api+key"
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-sm text-blue-600 hover:underline"
                    >
                        📺 How to get API keys
                    </a>
                </div>

                <div className="grid md:grid-cols-2 gap-6">
                    <div>
                        <Label htmlFor="ai_provider">AI Provider</Label>
                        <Select value={formData.ai_provider} onValueChange={(value) => onChange('ai_provider', value)}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select provider" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="openai">OpenAI</SelectItem>
                                <SelectItem value="anthropic">Anthropic (Claude)</SelectItem>
                                <SelectItem value="google">Google AI</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div>
                        <Label htmlFor="ai_model">AI Model</Label>
                        <Input
                            id="ai_model"
                            value={formData.ai_model}
                            onChange={(e) => onChange('ai_model', e.target.value)}
                            placeholder="gpt-4, claude-3, etc."
                        />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="ai_api_key">AI API Key</Label>
                        <Input
                            id="ai_api_key"
                            type="password"
                            value={formData.ai_api_key}
                            onChange={(e) => onChange('ai_api_key', e.target.value)}
                            placeholder="sk-..."
                        />
                    </div>
                </div>
            </div>

            {/* Google API Section */}
            <div className="space-y-4 border-b pb-6">
                <div className="flex items-center justify-between">
                    <h3 className="text-lg font-semibold">Google Integration</h3>
                    <a
                        href="https://www.youtube.com/results?search_query=how+to+get+google+calendar+api+key"
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-sm text-blue-600 hover:underline"
                    >
                        📺 Setup tutorial
                    </a>
                </div>

                <div className="grid md:grid-cols-2 gap-6">
                    <div>
                        <Label htmlFor="google_calendar_api_key">Google Calendar API Key</Label>
                        <Input
                            id="google_calendar_api_key"
                            type="password"
                            value={formData.google_calendar_api_key}
                            onChange={(e) => onChange('google_calendar_api_key', e.target.value)}
                            placeholder="Enter API key"
                        />
                    </div>

                    <div>
                        <Label htmlFor="google_meet_api_key">Google Meet API Key</Label>
                        <Input
                            id="google_meet_api_key"
                            type="password"
                            value={formData.google_meet_api_key}
                            onChange={(e) => onChange('google_meet_api_key', e.target.value)}
                            placeholder="Enter API key"
                        />
                    </div>
                </div>
            </div>

            {/* SMTP Section */}
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <h3 className="text-lg font-semibold">SMTP Configuration</h3>
                    <a
                        href="https://www.youtube.com/results?search_query=how+to+configure+smtp+email"
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-sm text-blue-600 hover:underline"
                    >
                        📺 SMTP setup guide
                    </a>
                </div>

                <div className="grid md:grid-cols-2 gap-6">
                    <div>
                        <Label htmlFor="smtp_host">SMTP Host</Label>
                        <Input
                            id="smtp_host"
                            value={formData.smtp_host}
                            onChange={(e) => onChange('smtp_host', e.target.value)}
                            placeholder="smtp.gmail.com"
                        />
                    </div>

                    <div>
                        <Label htmlFor="smtp_port">SMTP Port</Label>
                        <Input
                            id="smtp_port"
                            type="number"
                            value={formData.smtp_port}
                            onChange={(e) => onChange('smtp_port', parseInt(e.target.value))}
                            placeholder="587"
                        />
                    </div>

                    <div>
                        <Label htmlFor="smtp_username">SMTP Username</Label>
                        <Input
                            id="smtp_username"
                            value={formData.smtp_username}
                            onChange={(e) => onChange('smtp_username', e.target.value)}
                            placeholder="user@example.com"
                        />
                    </div>

                    <div>
                        <Label htmlFor="smtp_password">SMTP Password</Label>
                        <Input
                            id="smtp_password"
                            type="password"
                            value={formData.smtp_password}
                            onChange={(e) => onChange('smtp_password', e.target.value)}
                            placeholder="Enter password"
                        />
                    </div>

                    <div>
                        <Label htmlFor="smtp_encryption">Encryption</Label>
                        <Select
                            value={formData.smtp_encryption}
                            onValueChange={(value) => onChange('smtp_encryption', value)}
                        >
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="tls">TLS</SelectItem>
                                <SelectItem value="ssl">SSL</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div>
                        <Label htmlFor="smtp_from_address">From Email</Label>
                        <Input
                            id="smtp_from_address"
                            type="email"
                            value={formData.smtp_from_address}
                            onChange={(e) => onChange('smtp_from_address', e.target.value)}
                            placeholder="noreply@company.com"
                        />
                    </div>

                    <div className="grid gap-2 md:col-span-2">
                        <Label htmlFor="smtp_from_name">From Name</Label>
                        <Input
                            id="smtp_from_name"
                            value={formData.smtp_from_name}
                            onChange={(e) => onChange('smtp_from_name', e.target.value)}
                            placeholder="Company Name"
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
