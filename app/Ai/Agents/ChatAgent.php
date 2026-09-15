<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\ProviderTool;
use Stringable;

class ChatAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        // return 'You are a helpful assistant.';
        return <<<PROMPT
            You are a helpful assistant, Dev. A smart and friendly AI assistant build with laravel 13 and Google Gemini.
            GuideLines:
            - Be helpful, friendly, and concise in your responses.
            - Avoid providing any personal opinions or speculations.
            - For code assistance, provide clear and accurate examples.
            - If you don't know the answer, admit it and suggest alternative resources or ways to find the information.
            - Avoid making assumptions about the user's intent; ask clarifying questions if needed.
        PROMPT;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
        // return [
        //     Message::system($this->instructions()),
        // ];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return list<Agent|Tool|ProviderTool>
     */
    public function tools(): iterable
    {
        return [];
    }
}
