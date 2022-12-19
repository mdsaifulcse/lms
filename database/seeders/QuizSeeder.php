<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quizzes = [

            [
                'id'=>'1',
                'question'=>'কোন বাক্যে ক্রিয়া পদ উহ্য রয়েছে?',
                'topic'=>Quiz::quizTopics()['bangla'],
                'detail'=>'Quiz Details',
                'option1'=>'তিনি সাঁতার কাটছেন',
                'option2'=>'ইনি আমার ভাই',
                'option3'=>'কবির বই পড়ছে',
                'option4'=>'আমি লিখছি',
                'correct_option'=>'option2',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ], [
                'id'=>'2',
                'question'=>'ক্রিয়া সম্পাদনের যন্ত্র,উপকরণ বা সহায়ককে কি বলে?',
                'topic'=>Quiz::quizTopics()['bangla'],
                'detail'=>'Quiz Details',
                'option1'=>'অপাদান কারক',
                'option2'=>'করণকারক',
                'option3'=>'কর্মকারক',
                'option4'=>'অধিকরন কারক',
                'correct_option'=>'option2',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'3',
                'question'=>'মিথ্যাবাদী রাখাল’ কাব্যটির রচয়িতা কে?',
                'topic'=>Quiz::quizTopics()['bangla'],
                'detail'=>'Quiz Details',
                'option1'=>'সৈয়দ শামসুল হক',
                'option2'=>'মোহাম্মদ মনিরুজ্জামান',
                'option3'=>'আব্দুল মান্নান',
                'option4'=>'সৈয়দ আল মাহমুদ',
                'correct_option'=>'option4',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'4',
                'question'=>'বাঙ্গালা ভাষার ইতিবৃত্ত’ কে রচনা করেন?',
                'topic'=>Quiz::quizTopics()['bangla'],
                'detail'=>'Quiz Details',
                'option1'=>'সুনীতিকুমার চট্টোপাধ্যায়',
                'option2'=>'সুকুমার সেন',
                'option3'=>'মুহম্মদ শহীদুল্লাহ্‌',
                'option4'=>'মুহম্মদ এনামুল হক',
                'correct_option'=>'option3',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'5',
                'question'=>'ভাষার ক্ষুদ্রতম একক কোন্টি?',
                'topic'=>Quiz::quizTopics()['bangla'],
                'detail'=>'Quiz Details',
                'option1'=>'বর্ণ',
                'option2'=>'শব্দ',
                'option3'=>'অক্ষর',
                'option4'=>'ধ্বনি',
                'correct_option'=>'option4',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],

            // For math---------
            [
                'id'=>'6',
                'question'=>'বৃত্তের পরিধি ও ব্যাসের অনুপাত-?',
                'topic'=>Quiz::quizTopics()['math'],
                'detail'=>'Quiz Details',
                'option1'=>'3',
                'option2'=>'5',
                'option3'=>'২৫/৯',
                'option4'=>'২২ /৭',
                'correct_option'=>'option4',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'7',
                'question'=>'কোন চতুর্ভুজের বিপরীত কৌণিক বিন্দুর সংযোজন রেখাংশ দুটির প্রত্যেকটিকে বলে-',
                'topic'=>Quiz::quizTopics()['math'],
                'detail'=>'Quiz Details',
                'option1'=>'ভূমি',
                'option2'=>'কর্ণ',
                'option3'=>'মধ্যমা',
                'option4'=>'উচ্চতা',
                'correct_option'=>'option2',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'8',
                'question'=>'ত্রিভুজের বৃহত্তর বাহু সংলগ্ন কোণদ্বয় –',
                'topic'=>Quiz::quizTopics()['math'],
                'detail'=>'Quiz Details',
                'option1'=>'সূক্ষ্মকোণ',
                'option2'=>'স্থুলকোণ',
                'option3'=>'সমকোণ',
                'option4'=>'সরলকোণ',
                'correct_option'=>'option1',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'9',
                'question'=>'১২ ও ৯৬ এর মধ্যে (এই দুটি সংখ্যাসহ) কয়টি সংখ্যা ৪ দ্বারা বিভাজ্য?',
                'topic'=>Quiz::quizTopics()['math'],
                'detail'=>'Quiz Details',
                'option1'=>'24',
                'option2'=>'23',
                'option3'=>'22',
                'option4'=>'21',
                'correct_option'=>'option3',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],

            [
                'id'=>'10',
                'question'=>'একটি সোনার গয়নায় ওজন ৩২ গ্রাম। এতে সোনা ও তামার অনুপাত ৩ঃ১ । এতে কি পরিমাণ সোনা মেশালে অনুপাত ৪ঃ১ হবে?',
                'topic'=>Quiz::quizTopics()['math'],
                'detail'=>'Quiz Details',
                'option1'=>'২ গ্রাম',
                'option2'=>'3 গ্রাম',
                'option3'=>'6 গ্রাম',
                'option4'=>'8 গ্রাম',
                'correct_option'=>'option3',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            // For english ----
            [
                'id'=>'11',
                'question'=>'How many types of gender are there?',
                'topic'=>Quiz::quizTopics()['english'],
                'detail'=>'Quiz Details',
                'option1'=>'Four types',
                'option2'=>'Three types',
                'option3'=>'Two types',
                'option4'=>'One types',
                'correct_option'=>'option1',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'12',
                'question'=>'What is the meaning of the word ‘Nascent’?',
                'topic'=>Quiz::quizTopics()['english'],
                'detail'=>'Quiz Details',
                'option1'=>'Trail',
                'option2'=>'Beginning',
                'option3'=>'Nasal',
                'option4'=>'Odorous',
                'correct_option'=>'option2',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'13',
                'question'=>'What is the antonym of the word ‘Somber’?',
                'topic'=>Quiz::quizTopics()['english'],
                'detail'=>'Quiz Details',
                'option1'=>'Bright',
                'option2'=>'Dark',
                'option3'=>'Dismal',
                'option4'=>'Gloomy',
                'correct_option'=>'option1',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'14',
                'question'=>'What is the synonym of ‘Remember’-',
                'topic'=>Quiz::quizTopics()['english'],
                'detail'=>'Quiz Details',
                'option1'=>'Call up',
                'option2'=>'Memory',
                'option3'=>'Forget',
                'option4'=>'Recollect',
                'correct_option'=>'option4',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'15',
                'question'=>'What is the antonym of ‘Expel’?',
                'topic'=>Quiz::quizTopics()['english'],
                'detail'=>'Quiz Details',
                'option1'=>'Eject',
                'option2'=>'Dismiss',
                'option3'=>'Admit',
                'option4'=>'Banish',
                'correct_option'=>'option3',
                'correct_mark'=>Quiz::DEFAULTCORRECTMARK,
                'status'=>Quiz::ACTIVE,
                'created_by'=>2,
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],

        ];

        $quiz=\App\Models\Quiz::first();

        if (empty($quiz)){
            \App\Models\Quiz::insert($quizzes);
        }
    }
}
