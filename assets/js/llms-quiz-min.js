jQuery(document).ready(function($){$("#llms_start_quiz").click(function(){return get_quiz_questions(),!1})}),get_quiz_questions=function(){var u=jQuery("#llms-quiz").val(),e=jQuery("#llms-user").val();console.log(u+" "+e);var i=new Ajax("post",{action:"get_quiz_questions",quiz_id:u,user_id:e},!0);i.get_quiz_questions(u,e)},get_quiz_full_page=function(u,e){console.log("get quiz full page called"),console.log(u)};