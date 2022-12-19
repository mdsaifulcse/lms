@extends('admin.layout.app')
@section('title')
    Dashboard | Client
@endsection

@section('main-content')
    <div class="page-wrapper">
        <div class="page-header">
            <div class="page-header-title">
                <h4>  Result for <strong>{{$topic}}</strong> Quiz, UserName: {{$user->name}}</h4>
            </div>
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="index-2.html">
                            <i class="icofont icofont-home"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Pages</a>
                    </li>
                    <li class="breadcrumb-item"><a href="#!">Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="page-body">
            <div class="row">
                <div class="col-md-12 col-xl-12">
                    <div class="card ">
                        <form class="" action="{{route('user-quiz.store')}}" method="POST" style="padding: 15px;">
                            @csrf

                            @forelse($topicWiseQuizzes as $key=>$topicWiseQuiz)
                            <div class="single-quiz">
                                <h4 class="entry-title mt-5">{{$key+1}}.  {{$topicWiseQuiz->question}}</h4>
                                @if(!empty($topicWiseQuiz->quizAnswer))
                                    <?php
                                    $answerOption=$topicWiseQuiz->quizAnswer->answer_option;
                                    $correctOption=$topicWiseQuiz->correct_option;
                                    ?>


                                <span>Correct answer is: {{$topicWiseQuiz->$correctOption}} </span><br>
                                <span class="{{$topicWiseQuiz->correct_option==$topicWiseQuiz->quizAnswer->answer_option?'text-success':'text-danger'}}">
                                    Your Ans: {{$topicWiseQuiz->$answerOption}}</span>
                                    @else
                                    <span class="text-warning">No answer</span>
                                @endif
                                <hr>

                                <ul>
                                    <li class="" >
                                        <label for="{{'1'.$key}}"><input type="radio" id="{{'1'.$key}}" name="answer_option[{{$topicWiseQuiz->id}}]" value="option1"> {{$topicWiseQuiz->option1}}</label>
                                    </li>
                                    <li>
                                        <label for="{{'2'.$key}}"><input type="radio" id="{{'2'.$key}}" name="answer_option[{{$topicWiseQuiz->id}}]" value="option2"> {{$topicWiseQuiz->option2}}</label>
                                    </li>
                                    <li>
                                        <label for="{{'3'.$key}}"><input type="radio" id="{{'3'.$key}}" name="answer_option[{{$topicWiseQuiz->id}}]" value="option3"> {{$topicWiseQuiz->option3}}</label>
                                    </li>
                                    <li>
                                        <label for="{{'4'.$key}}"><input type="radio" id="{{'4'.$key}}" name="answer_option[{{$topicWiseQuiz->id}}]" value="option4"> {{$topicWiseQuiz->option4}}</label>
                                    </li>
                                </ul>
                            </div>

                                @empty
                                <h2> Data not found </h2>
                            @endforelse

                            {{--<button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Submit Answer.</button>--}}

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection