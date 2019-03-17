<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Users\User;

class IncredBotsTestConversation extends Conversation
{
    /** @var User*/
    protected $userData;

    /** @var int */
    protected $userAge;

    public function run() : void
    {
        $this->startConversation();
    }

    public function startConversation() : void
    {
        $this->userData = $this->getBot()->getUser();

        $this->say('Cześć ' . $this->userData->getFirstName());

        $this->askAboutUserAge();
    }

    public function askAboutUserAge() : void
    {
        $this->ask('Ile masz lat?', function (Answer $enteredAge) {

            $this->userAge = $enteredAge->getText();

            if ($this->checkIfAgeEnteredByUserIsCorrect($this->userAge)) {
                $this->say('Dziękuję.');
                $this->confirmUserYearOfBirthBasedOnUserAge($this->userAge);
            } else {
                $this->repeat('Proszę o podanie wieku w zakresie 13 - 100 lat');
            }
        });
    }


    public function confirmUserYearOfBirthBasedOnUserAge(int $userAge) : void
    {
        $yearOfBirth = $this->obtainUserYearOfBirthBasedOnUserAge($userAge);
        $questionAboutUserYearOfBirth = Question::create("Twój rok urodzenia to $yearOfBirth ?")
            ->fallback('Unable to ask question')
            ->callbackId('agree')
            ->addButtons([
                Button::create('TAK')->value('Yes'),
                Button::create('NIE')->value('No')
            ]);

        $this->ask($questionAboutUserYearOfBirth, function (Answer $confirmationOfUserYearOfBirth) {
            if ($confirmationOfUserYearOfBirth->isInteractiveMessageReply()) {
                if ($confirmationOfUserYearOfBirth->getValue() === 'Yes') {
                    $this->say('Świetnie. Dziękuję za odpowiedz.');
                } else {
                    $this->askAboutUserAge();
                }
            } else {
                $this->say('Proszę nacisnąć jeden z przycisków');
                $this->confirmUserYearOfBirthBasedOnUserAge($this->userAge);
            }
        });
    }

    private function checkIfAgeEnteredByUserIsCorrect(int $userAge) : bool
    {
        return $userAge >= 13 && $userAge <= 100;
    }

    private function obtainUserYearOfBirthBasedOnUserAge(int $userAge) : int
    {
        return date('Y') - (int)$userAge;
    }
}