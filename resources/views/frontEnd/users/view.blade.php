@extends('frontEnd.layout')
@section('content')

<section class="inner-pading account">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>{{$labels['my_account']}}</h2>
            </div>
            <div class="col-lg-3">
                <div class="account-left">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.0005 0C4.48541 0 0.000488281 4.48492 0.000488281 10C0.000488281 15.5151 4.48541 20 10.0005 20C15.5156 20 20.0005 15.5151 20.0005 10C20.0005 4.48492 15.5156 0 10.0005 0ZM10.0005 1.33341C14.7949 1.33341 18.6672 5.20572 18.6672 10.0001C18.6672 11.6229 18.2248 13.1404 17.4519 14.4374C15.621 12.3944 12.957 11.111 10.0007 11.111C7.04077 11.111 4.37886 12.3971 2.5495 14.4444C1.77477 13.1462 1.33422 11.6247 1.33422 9.99992C1.33422 5.20548 5.20652 1.33325 10.0009 1.33325L10.0005 1.33341ZM10.0005 2.00012C7.6766 2.00012 5.77827 3.89837 5.77827 6.22234C5.77827 8.54631 7.67652 10.4446 10.0005 10.4446C12.3245 10.4446 14.2227 8.54631 14.2227 6.22234C14.2227 3.89837 12.3245 2.00012 10.0005 2.00012ZM10.0005 3.33353C11.6039 3.33353 12.8894 4.61905 12.8894 6.22246C12.8894 7.82587 11.6039 9.11139 10.0005 9.11139C8.39708 9.11139 7.11156 7.82587 7.11156 6.22246C7.11156 4.61905 8.39708 3.33353 10.0005 3.33353ZM10.0005 12.4446C12.6764 12.4446 15.0568 13.6601 16.6465 15.5627C15.058 17.4598 12.6747 18.6669 10.0005 18.6669C7.32628 18.6669 4.94295 17.46 3.35446 15.5627C4.94299 13.6581 7.32271 12.4446 10.0005 12.4446Z" fill="white"/>
                            </svg>
                            {{$labels['profile']}}</button>
                        <button class="nav-link" id="v-pills-Change-password-tab" data-bs-toggle="pill" data-bs-target="#v-pills-Change-password" type="button" role="tab" aria-controls="v-pills-Change-password" aria-selected="false">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.57242 5.16162C8.45721 5.16162 7.52781 6.09102 7.52781 7.20623V8.0116H7.4658C6.84629 8.0116 6.35059 8.5073 6.35059 9.12681V13.3707C6.35059 13.9903 6.84628 14.486 7.4658 14.486H11.7097C12.3292 14.486 12.8249 13.9903 12.8249 13.3707L12.8252 9.12681C12.8252 8.5073 12.3295 8.0116 11.7099 8.0116H11.6479V7.20623C11.617 6.05987 10.6877 5.16162 9.5724 5.16162H9.57242ZM8.30226 7.20623C8.30226 6.49379 8.85976 5.93607 9.57242 5.93607C10.2849 5.93607 10.8426 6.49357 10.8426 7.20623V8.0116H8.30226V7.20623ZM12.0507 9.12681V13.3707C12.0507 13.5566 11.8958 13.7115 11.7099 13.7115H7.46601C7.28018 13.7115 7.12524 13.5566 7.12524 13.3707V9.12681C7.12524 8.94097 7.28018 8.78604 7.46601 8.78604H11.7099C11.8958 8.78604 12.0507 8.94097 12.0507 9.12681Z" fill="black"/>
                                <path d="M17.1311 4.23218C17.0073 4.07724 16.7594 4.04634 16.5736 4.17016C16.4187 4.29398 16.3878 4.54183 16.5116 4.72765C17.7506 6.27656 18.3704 8.16621 18.3704 10.1488C18.3704 15.0125 14.4052 18.9466 9.57262 18.9466C7.99279 18.9466 6.44389 18.5129 5.11173 17.7384L6.28895 17.7075C6.5059 17.7075 6.66062 17.5217 6.66062 17.3047C6.66062 17.0878 6.47478 16.8711 6.25784 16.9331L3.90356 17.026H3.84154H3.81064C3.68683 17.026 3.56279 17.088 3.50077 17.2118C3.43876 17.3047 3.40786 17.4597 3.46988 17.5526L4.33726 19.7522C4.39928 19.9071 4.55421 20 4.70893 20C4.77095 20 4.80185 20 4.86386 19.9691C5.0497 19.8762 5.17373 19.6592 5.08082 19.4734L4.6158 18.3273C6.10269 19.2255 7.80654 19.7212 9.5724 19.7212C14.8698 19.7212 19.1446 15.4153 19.1446 10.149C19.1446 7.98061 18.4631 5.936 17.1312 4.23221L17.1311 4.23218Z" fill="black"/>
                                <path d="M9.57241 1.35122C10.9355 1.35122 12.2985 1.69199 13.5067 2.28061L12.3295 2.34263C12.1125 2.34263 11.9578 2.52846 11.9578 2.74541C11.9578 2.96236 12.1436 3.11708 12.3295 3.11708H12.3604L14.8075 2.96215C14.9313 2.96215 15.0554 2.90013 15.1174 2.77631C15.1794 2.65249 15.2103 2.52846 15.1483 2.43554L14.25 0.23599C14.1571 0.0501551 13.9402 -0.0427623 13.7543 0.0190389C13.5685 0.111956 13.4756 0.328908 13.5374 0.514737L14.002 1.62995C12.6389 0.917507 11.152 0.545605 9.57221 0.545605C4.27486 0.545605 0 4.85155 0 10.1178C0 12.0384 0.557494 13.8971 1.64184 15.508C1.70385 15.6319 1.82767 15.663 1.9517 15.663C2.01372 15.663 2.10664 15.6321 2.16866 15.601C2.35449 15.4772 2.38561 15.2293 2.26157 15.0744C1.27017 13.6184 0.743594 11.9146 0.743594 10.1178C0.774711 5.28526 4.70878 1.35099 9.57233 1.35099L9.57241 1.35122Z" fill="black"/>
                            </svg>
                            {{$labels['change_password']}}
                        </button>
                        <button href="favourites.html" class="nav-link" id="v-pills-favourite-tab" data-bs-toggle="pill" data-bs-target="#v-pills-favourite" type="button" role="tab" aria-controls="v-pills-favourite" aria-selected="false">
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 14.3936C7.484 13.9362 6.9008 13.4605 6.284 12.9543H6.276C4.10401 11.1792 1.64241 9.17065 0.555217 6.76387C0.198032 5.99764 0.00874142 5.16398 9.12476e-06 4.31871C-0.0023763 3.15889 0.463034 2.04705 1.29106 1.23449C2.11909 0.421925 3.23984 -0.0227734 4.40001 0.000898967C5.34451 0.00239005 6.26867 0.275225 7.0624 0.786901C7.41118 1.01316 7.72674 1.28685 8 1.60009C8.27479 1.28808 8.59044 1.01455 8.9384 0.786901C9.73178 0.275124 10.6557 0.00227311 11.6 0.000898967C12.7602 -0.0227734 13.8809 0.421925 14.7089 1.23449C15.537 2.04705 16.0024 3.15889 16 4.31871C15.9918 5.16533 15.8025 6.00041 15.4448 6.76787C14.3576 9.17465 11.8968 11.1824 9.7248 12.9543L9.7168 12.9607C9.0992 13.4637 8.5168 13.9394 8.0008 14.4L8 14.3936ZM4.40001 1.60009C3.65482 1.59077 2.93607 1.87582 2.40001 2.39329C1.88352 2.90036 1.59486 3.59511 1.59995 4.31871C1.60908 4.9348 1.74868 5.54197 2.00961 6.10021C2.52282 7.13865 3.2153 8.07845 4.05521 8.8764C4.84801 9.67599 5.76 10.45 6.5488 11.1009C6.7672 11.2808 6.9896 11.4623 7.212 11.6438L7.352 11.7581C7.5656 11.9325 7.7864 12.1132 8 12.2907L8.0104 12.2811L8.0152 12.2771H8.02L8.0272 12.2715H8.0312H8.0352L8.0496 12.2595L8.0824 12.2331L8.088 12.2283L8.0968 12.2219H8.1016L8.1088 12.2155L8.64 11.7797L8.7792 11.6654C9.004 11.4823 9.2264 11.3008 9.4448 11.1209C10.2336 10.47 11.1464 9.69678 11.9392 8.89319C12.7792 8.09564 13.4717 7.15608 13.9848 6.1178C14.2504 5.55472 14.392 4.9412 14.4 4.31871C14.4033 3.59735 14.1148 2.90526 13.6 2.39968C13.065 1.87988 12.3461 1.59248 11.6 1.60009C10.6895 1.59236 9.81915 1.97379 9.208 2.64836L8 4.03965L6.792 2.64836C6.18085 1.97379 5.31047 1.59236 4.40001 1.60009Z" fill="#424458"/>
                            </svg>
                                                              
                            {{$labels['favourites']}}
                        </button>
                        <a href="subscription-plan.html" class="nav-link" >
                            <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.13411 6.43936H1.6791C1.25784 6.46199 0.844693 6.31684 0.530095 6.03579C0.215482 5.75488 0.0249424 5.36069 0 4.93958V1.49178C0.0275517 1.07248 0.219182 0.680932 0.533641 0.402192C0.847947 0.12329 1.25953 -0.0203317 1.6791 0.00232532H3.13411C3.22554 0.00232532 3.31312 0.0386506 3.37792 0.103297C3.44257 0.167942 3.47889 0.255678 3.47889 0.347106V6.0945C3.47889 6.18593 3.44257 6.27366 3.37792 6.33831C3.31312 6.40295 3.22554 6.43928 3.13411 6.43928V6.43936ZM1.6791 0.691965C1.44145 0.67057 1.20488 0.743068 1.02 0.893908C0.835141 1.0446 0.71647 1.26178 0.689534 1.49883V4.94664C0.718932 5.1826 0.838219 5.39808 1.02262 5.54833C1.20686 5.69871 1.44207 5.77213 1.67908 5.75335H2.78931V0.691969L1.6791 0.691965Z" fill="black"/>
                                <path d="M8.27476 19.9998C8.20288 19.9993 8.133 19.9764 8.07482 19.9342L6.7922 19.0171L5.71305 19.7826C5.5933 19.8677 5.43277 19.8677 5.31303 19.7826L4.2476 19.0171L3.33746 19.655C3.23202 19.7301 3.09349 19.7398 2.97867 19.6801C2.86384 19.6205 2.79196 19.5017 2.79273 19.3723V0.347466C2.79273 0.157068 2.94711 0.00268555 3.13751 0.00268555H18.2732C18.3646 0.00268555 18.4523 0.0390108 18.517 0.103657C18.5816 0.168303 18.618 0.256038 18.618 0.347466V19.221C18.6173 19.3496 18.545 19.4674 18.4305 19.5262C18.3158 19.5848 18.1781 19.5748 18.0732 19.5002L17.4043 19.0175L16.3251 19.7829C16.2054 19.868 16.0449 19.868 15.9253 19.7829L14.8598 19.0175L13.791 19.7863C13.6713 19.8716 13.5107 19.8716 13.3912 19.7863L12.3085 19.0175L11.2258 19.7829C11.1062 19.868 10.9456 19.868 10.8259 19.7829L9.76048 19.0175L8.46419 19.9345C8.40893 19.9747 8.3429 19.9975 8.27456 20.0001L8.27476 19.9998ZM6.79221 18.2517C6.86409 18.2522 6.93397 18.2751 6.99215 18.3173L8.27477 19.231L9.57107 18.3139V18.3138C9.69082 18.2286 9.85135 18.2286 9.97109 18.3138L11.033 19.0792L12.1157 18.3138C12.2353 18.2286 12.3958 18.2286 12.5155 18.3138L13.581 19.0792L14.6637 18.3138H14.6635C14.7832 18.2286 14.9438 18.2286 15.0635 18.3138L16.129 19.0792L17.2116 18.3138H17.2115C17.3311 18.2286 17.4918 18.2286 17.6114 18.3138L17.9355 18.5482V0.6921H3.48231V18.7101L4.04783 18.3137C4.16743 18.2285 4.32797 18.2285 4.4477 18.3137L5.51652 19.0791L6.59921 18.3137C6.65585 18.2741 6.72311 18.2526 6.79222 18.2516L6.79221 18.2517Z" fill="black"/>
                                <path d="M15.4947 4.82945H6.29595C6.10555 4.82945 5.95117 4.67507 5.95117 4.48467C5.95117 4.29428 6.10555 4.13989 6.29595 4.13989H15.4947C15.6851 4.13989 15.8395 4.29428 15.8395 4.48467C15.8395 4.67507 15.6851 4.82945 15.4947 4.82945Z" fill="black"/>
                                <path d="M15.4947 8.62193H6.29595C6.10555 8.62193 5.95117 8.46755 5.95117 8.27715C5.95117 8.08676 6.10555 7.93237 6.29595 7.93237H15.4947C15.6851 7.93237 15.8395 8.08676 15.8395 8.27715C15.8395 8.46755 15.6851 8.62193 15.4947 8.62193Z" fill="black"/>
                                <path d="M9.309 16.4553C9.22681 16.4553 9.14738 16.4259 9.08489 16.3724L5.74745 13.5522C5.60169 13.4255 5.5863 13.2048 5.71298 13.0592C5.8395 12.9134 6.06037 12.898 6.20599 13.0247L9.30902 15.6726L15.2944 11.3799C15.3683 11.3219 15.4625 11.2965 15.5555 11.3095C15.6486 11.3225 15.7322 11.373 15.7871 11.4491C15.8421 11.5252 15.8636 11.6205 15.8468 11.7129C15.8299 11.8052 15.7762 11.8867 15.6978 11.9385L9.50912 16.3895C9.45094 16.4317 9.38106 16.4546 9.30918 16.4551L9.309 16.4553Z" fill="black"/>
                            </svg>
                            {{$labels['subscription_plan']}}</a>
                        <a href="{{route('frontend.faqs')}}" class="nav-link" >
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.9998 0C4.48574 0 0 4.48591 0 9.9998C0 11.8334 0.500901 13.6216 1.45044 15.1843L0.0206317 19.4734C-0.02724 19.6172 0.0100909 19.7758 0.117256 19.8827C0.193458 19.9592 0.295789 20 0.400093 20C0.442476 20 0.485076 19.9934 0.526581 19.9796L4.81573 18.5498C6.37816 19.4989 8.16635 19.9998 9.99997 19.9998C15.514 19.9998 19.9998 15.5138 19.9998 9.99996C19.9998 4.48607 15.5139 0.000158263 9.99997 0.000158263L9.9998 0ZM9.9998 19.2002C8.254 19.2002 6.55322 18.7066 5.0813 17.773C5.01652 17.732 4.94208 17.7109 4.86698 17.7109C4.82437 17.7109 4.78177 17.7177 4.74049 17.7313L1.03239 18.9676L2.2687 15.2595C2.30691 15.1456 2.29132 15.0202 2.2272 14.9187C1.29327 13.4467 0.799805 11.746 0.799805 10.0002C0.799805 4.92734 4.92689 0.80037 9.99964 0.80037C15.0724 0.80037 19.1995 4.92745 19.1995 10.0002C19.1995 15.073 15.0724 19.2 9.99964 19.2L9.9998 19.2002Z" fill="black"/>
                                <path d="M10.1869 2.30331C8.85248 2.25522 7.574 2.74074 6.61501 3.66437C5.65033 4.59284 5.11914 5.84164 5.11914 7.18055C5.11914 7.40146 5.29833 7.58065 5.51924 7.58065H7.37327C7.59418 7.58065 7.77315 7.40146 7.77315 7.18055C7.77315 6.56964 8.01559 6 8.45568 5.57618C8.87269 5.17476 9.42103 4.95364 10.0001 4.95364L10.0877 4.95518C11.223 4.99866 12.1816 5.9574 12.2246 7.09208C12.2613 8.0572 11.6798 8.9312 10.7773 9.26715C9.49903 9.74412 8.67265 10.9082 8.67265 12.2328V13.5919C8.67265 13.8128 8.85184 13.992 9.07276 13.992H10.9266C11.1475 13.992 11.3267 13.8128 11.3267 13.5919V12.2328C11.3267 12.0277 11.4749 11.8397 11.7046 11.7543C13.6823 11.0167 14.957 9.10246 14.8765 6.99169C14.7803 4.45976 12.7211 2.40015 10.187 2.30334L10.1869 2.30331ZM11.4252 11.0051C10.8793 11.2086 10.5269 11.6906 10.5269 12.2333V13.1925H9.47306V12.2333C9.47306 11.246 10.0949 10.3762 11.057 10.0173C12.2834 9.56058 13.074 8.37301 13.0244 7.06204C12.9657 5.51918 11.6622 4.21563 10.1167 4.15615L10.0001 4.15395C9.21327 4.15395 8.46772 4.45458 7.90095 5.00007C7.40005 5.4823 7.08755 6.10377 6.99907 6.78079H5.93843C6.0311 5.81414 6.46019 4.9239 7.17013 4.24092C7.93389 3.50527 8.93831 3.10012 9.99766 3.10012C10.0517 3.10012 10.1064 3.10122 10.1586 3.10297C12.2755 3.18378 13.9967 4.90561 14.0771 7.02256C14.1445 8.78787 13.0788 10.3883 11.4252 11.0052L11.4252 11.0051Z" fill="black"/>
                                <path d="M10.9263 15.0459H9.07247C8.85155 15.0459 8.67236 15.2251 8.67236 15.446V17.3C8.67236 17.5209 8.85155 17.7001 9.07247 17.7001H10.9263C11.1472 17.7001 11.3264 17.5209 11.3264 17.3V15.446C11.3264 15.2251 11.1472 15.0459 10.9263 15.0459ZM10.5264 16.8999H9.4726V15.8459H10.5264V16.8999Z" fill="black"/>
                            </svg>   
                            {{$labels['faqs']}}</a>
                        <a href="#" onclick="logoutuser(this)" class="nav-link" >
                            <svg width="17" height="20" viewBox="0 0 17 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.57449 19.2309H1.15184C0.940783 19.2309 0.769258 19.0583 0.769258 18.8464V1.15399C0.769258 0.941864 0.940783 0.769445 1.15184 0.769445H8.57449C8.78679 0.769445 8.95903 0.597384 8.95903 0.384902C8.95903 0.172419 8.78679 0.000357717 8.57449 0.000357717L1.15184 0C0.516715 0 0 0.51763 0 1.15381V18.8462C0 19.4824 0.516898 20 1.15184 20H8.57449C8.78679 20 8.95903 19.8279 8.95903 19.6155C8.95921 19.403 8.78697 19.2309 8.57449 19.2309V19.2309ZM16.156 9.72813L11.7592 5.33145C11.6089 5.18122 11.3655 5.18122 11.2153 5.33145C11.065 5.48169 11.065 5.7253 11.2153 5.87537L14.9557 9.61568H4.64705C4.43474 9.61568 4.2625 9.78774 4.2625 10.0002C4.2625 10.2127 4.43474 10.3848 4.64705 10.3848H14.9557L11.2154 14.1251C11.0652 14.2751 11.0652 14.5187 11.2154 14.669C11.2905 14.7441 11.3891 14.7815 11.4874 14.7815C11.586 14.7815 11.6844 14.7441 11.7595 14.669L16.1563 10.2723C16.2284 10.2002 16.2688 10.1024 16.2688 10.0003C16.269 9.89814 16.2282 9.80048 16.1562 9.72822L16.156 9.72813Z" fill="black"/>
                            </svg>
                            {{$labels['logout']}}</a>
                        
                      </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                        <div class="account-detail-box">
                            <h3 class="account-heading">{{$labels['profile']}}</h3>
                            <div class="proile-box">
                                <img src="{{asset('assets/img/profile-img.png')}}" alt="image" />
                                <div class="detail-profile">
                                    <div class="detail-profile-box">
                                        <h4 class="text-left">Albina Frami</h4>
                                        <p>albina123@gmail.com</p>
                                        <span class="badge badge-user">USER</span>
                                    </div>
                                    <span class="edit-icon" data-bs-toggle="modal" data-bs-target="#exampleModal"><img src="assets/img/edit.svg" alt="icon" /></span>
                                </div>
                            </div>
                            <div class="user-about">
                                <h5>About</h5>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem nibh volutpat amet faucibus venenatis. Massa varius id semper nunc sapien.</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-Change-password" role="tabpanel" aria-labelledby="v-pills-Change-password-tab">
                        <div class="account-detail-box">
                            <h3 class="account-heading">Change Password</h3>
                            <form action="">
                                <div class="input-outer ">
                                    <div class="hide-show-password">
                                        <input type="password" placeholder="Current Password">
                                        <div class="toggle-password">
                                            <span class="show-password">Show</span>
                                            <span class="hide-password">Hide</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-outer ">
                                    <div class="hide-show-password">
                                        <input type="password" placeholder="New Password">
                                        <div class="toggle-password">
                                            <span class="show-password">Show</span>
                                            <span class="hide-password">Hide</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-outer ">
                                    <div class="hide-show-password">
                                        <input type="password" placeholder="Confirm Password">
                                        <div class="toggle-password">
                                            <span class="show-password">Show</span>
                                            <span class="hide-password">Hide</span>
                                        </div>
                                    </div>
                                </div>
                                <button class="comman-btn">Save</button>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade favourite" id="v-pills-favourite" role="tabpanel" aria-labelledby="v-pills-favourite-tab">
                        <div class="account-detail-box account-detail-box1">
                            <h3 class="mb-0 account-heading">Favourite</h3>
                            <div class="row">
                                <div class="col-xxl-4 col-lg-6 col-sm-6">
                                    <div class="featured-properties-box">
                                        <a href="featured-properties.html">
                                            <div class="featured-properties-img">
                                                <img src="assets/img/featured-properties6.jpg" alt="" />
                                                <span class="badge badge-buy">Buy</span>
                                            </div>
                                            <div class="featured-properties-box-content">
                                                <p> <img src="assets/img/location.svg" alt="location">Shuwaikh, Behind City Center</p>
                                                <h3>Property name goes here like this</h3>
                                                <h5>450 KD</h5>
                                                <div class="featured-properties-icon">
                                                    <span><img src="assets/img/Featured1.svg" alt="" />1</span>
                                                    <span><img src="assets/img/Featured2.svg" alt="" />1</span>
                                                    <span><img src="assets/img/Featured3.svg" alt="" />2</span>
                                                    <span><img src="assets/img/Featured4.svg" alt="" />210  Sqft</span>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="heart-icon-box">
                                            <img class="heart-icon heart-icon-fill" src="assets/img/heart-fill.svg" alt="" />
                                            <img class="heart-icon heart-icon-border" src="assets/img/heart.svg" alt="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-sm-6">
                                    <div class="featured-properties-box">
                                        <a href="featured-properties.html">
                                            <div class="featured-properties-img">
                                                <img src="assets/img/featured-properties6.jpg" alt="" />
                                                <span class="badge badge-buy">Buy</span>
                                            </div>
                                            <div class="featured-properties-box-content">
                                                <p> <img src="assets/img/location.svg" alt="location">Shuwaikh, Behind City Center</p>
                                                <h3>Property name goes here like this</h3>
                                                <h5>450 KD</h5>
                                                <div class="featured-properties-icon">
                                                    <span><img src="assets/img/Featured1.svg" alt="" />1</span>
                                                    <span><img src="assets/img/Featured2.svg" alt="" />1</span>
                                                    <span><img src="assets/img/Featured3.svg" alt="" />2</span>
                                                    <span><img src="assets/img/Featured4.svg" alt="" />210  Sqft</span>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="heart-icon-box">
                                            <img class="heart-icon heart-icon-fill" src="assets/img/heart-fill.svg" alt="" />
                                            <img class="heart-icon heart-icon-border" src="assets/img/heart.svg" alt="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-sm-6">
                                    <div class="featured-properties-box">
                                        <a href="featured-properties.html">
                                            <div class="featured-properties-img">
                                                <img src="assets/img/featured-properties6.jpg" alt="" />
                                                <span class="badge badge-buy">Buy</span>
                                            </div>
                                            <div class="featured-properties-box-content">
                                                <p> <img src="assets/img/location.svg" alt="location">Shuwaikh, Behind City Center</p>
                                                <h3>Property name goes here like this</h3>
                                                <h5>450 KD</h5>
                                                <div class="featured-properties-icon">
                                                    <span><img src="assets/img/Featured1.svg" alt="" />1</span>
                                                    <span><img src="assets/img/Featured2.svg" alt="" />1</span>
                                                    <span><img src="assets/img/Featured3.svg" alt="" />2</span>
                                                    <span><img src="assets/img/Featured4.svg" alt="" />210  Sqft</span>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="heart-icon-box">
                                            <img class="heart-icon heart-icon-fill" src="assets/img/heart-fill.svg" alt="" />
                                            <img class="heart-icon heart-icon-border" src="assets/img/heart.svg" alt="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
            </div>
        </div>
    </div>
</section>

@endsection
@push('after-scripts')
    
@endpush