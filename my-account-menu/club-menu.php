<?php
add_action('init', 'mohaazon_club_payment_menu_link');
function mohaazon_club_payment_menu_link(){
    add_rewrite_endpoint('club-payment', EP_ROOT | EP_PAGES);
}


add_filter('woocommerce_get_query_vars', 'mohaazon_club_payment_query_vars', 0);
function mohaazon_club_payment_query_vars($vars){
    $vars[] = 'club-payment';
    return $vars;
}


add_filter('woocommerce_account_menu_items','mohaazon_club_payment_menu_items');
function mohaazon_club_payment_menu_items($items){
    $new = array( 'club-payment' => 'Club Payment' );
    $items = array_slice( $items, -1, 0, true ) + $new + array_slice( $items, 0, NULL, true );
    unset($items['downloads']);
 
	return $items;
}


add_action( 'woocommerce_account_club-payment_endpoint', 'mohaazon_club_payment_menu_items_page2' );
function mohaazon_club_payment_menu_items_page2(){
    global $wpdb;
    $title = 'Due';
    $query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '$title' AND `post_status` = 'publish' AND `post_type` = 'product'";
    $product_id = $wpdb->get_var($query);
    $user_id = get_current_user_ID();
    $total_due = get_user_meta($user_id, 'total_due', true);
    $membership = get_user_meta($user_id, 'naims_membership', true);
    if($total_due >= 0){
        $account_balance = 0;
        $due = $total_due;
    }
    else{
        $account_balance = -$total_due;
        $due = 0;
    }

    if($membership == "lifetime" && $total_due > 0){
        ?>
            <div style="text-align: center;box-shadow: 7px 7px 7px 0px #cc4343;padding: 10px;border-radius: 10px;background: #c82424;color: #bfffa5; margin-bottom: 10px;"><b> Please pay your Lifetime Membership cost now or your membership will be cancelled very soon </b></div>
        <?php    
    }

    ?>
            <div style="text-align: center;box-shadow: 7px 7px 7px 0px #4aa5d1;padding: 10px;border-radius: 10px;background: #337ab7;color: #bfffa5; margin-bottom: 10px;"><b>Your Membership Plan : <?php echo $membership; ?></b></div>
            <div style="text-align: center;box-shadow: 7px 7px 7px 0px #51c040;padding: 10px;border-radius: 10px;background: #009936;color: #bfffa5; margin-bottom: 10px;"><b> Accounts & Payments </b></div>
           <table style="border-spacing: 5px;border-collapse: separate; width:100%;" class="pay-table table table-hover">
               <thead>
                   <tr style="box-shadow: 1px 2px 5px -1px #959595;">
                       <th scope="col" style="text-align: center">Serial</th>
                       <th scope="col"style="text-align: center">Membership</th>
                       <th scope="col" style="text-align: center">Total Due</th>
                       <th scope="col" style="text-align: center">Balance</th>
                       <th scope="col" style="text-align: center">Payment</th>
                   </tr>
               </thead>
               <tbody>
                    <tr style="box-shadow: 1px 2px 5px -1px #959595;" scope="row">
                        <td style="text-align: center">01</td>
                        <td style="text-align: center"><?php echo $membership; ?></td>
                        <td style="text-align: center"><?php echo $due.' Tk'; ?></td>
                        <td style="text-align: center"><?php echo $account_balance.' Tk'; ?></td>
                        <td style="text-align: center">
                            <a style="display: block; padding: 5px 10px; background: #009913;border-radius: 5px;color: #fff;box-shadow: 1px 1px 3px 3px #60ff78;" href="<?php echo wc_get_checkout_url().'?add-to-cart='.$product_id ?>">Pay an Amount</a>
                        </td>
                    </tr>
               </tbody>
           </table>
           <div>
                <a href="<?php echo site_url('/my-account/club-members/'); ?>" id="see-members-list" style="text-align: center;box-shadow: 7px 7px 7px 0px #cc4343;padding: 10px;border-radius: 10px;background: #c82424;color: #bfffa5; margin-bottom: 10px;">See Member's List</a>
            </div>
 
        </div><br><br><hr>
    <?php
}

//=======================================================================================================================================================================

function mohaazon_club_payment_menu_items_page() {
    global $wpdb;
    $title = 'LifeTime-Membership';
    $query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '$title' AND `post_status` = 'publish' AND `post_type` = 'product'";
    $product_id = $wpdb->get_var($query);

    //time interval for users to show product
        $registered_date = date( 'Y-m-d', strtotime("-1 month ".wp_get_current_user()->user_registered) ) ;
        $registered_date_time = date_create($registered_date);
        
        $current_date = date("Y-m-d", time());
        $current_date;
        //$current_date = date("Y-m-d", strtotime('10 day'));
        $current_date_time = date_create($current_date);
        $diff = date_diff($registered_date_time, $current_date_time);
        $days = $diff->format("%a days ago");
       // echo $days;

    $posts_per_page = 7;
    $round = round($posts_per_page/2);
    $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
    $products = new WP_Query(
        array(
            'post_type' => 'product',
            'post_status'=> 'publish',
            'posts_per_page'=> $posts_per_page,
            'paged'  =>  $paged,
            'post__not_in' => array($product_id),
            'orderby' => 'date',
            'order' => 'DESC',
        
            // Using the date_query to filter posts from last week
            'date_query' => array(
                array(
                    'after' => $days
                )
            )
        )
        );
        if(get_user_meta(get_current_user_ID(), 'lifetime_member', true) == 'no'){


        ?>

        <div>
        <div style="text-align: center;box-shadow: 7px 7px 7px 0px #51c040;padding: 10px;border-radius: 10px;background: #009936;color: #bfffa5; margin-bottom: 10px;"><b> Monthly Payment : </b></div>
           <table style="border-spacing: 5px;border-collapse: separate; width:100%;" class="table table-hover">
               <thead>
                   <tr style="box-shadow: 1px 2px 5px -1px #959595;">
                       <th scope="col" style="text-align: center">Serial</th>
                       <th scope="col">Month</th>
                       <th scope="col">Fees</th>
                       <th scope="col" style="text-align: center">Total Due</th>
                       <th scope="col" style="text-align: center">Payment</th>
                   </tr>
               </thead>
               <tbody>
                <?php
                $count = 0;
                if($products->have_posts()){
                    while($products->have_posts()){
                        $products->the_post();
                        $product_id = get_the_ID();
                        $count = $count+1;
                        ?>
                        <tr style="box-shadow: 1px 2px 5px -1px #959595;" scope="row">
                            <td style="text-align: center"><?php echo $count; ?></td>
                            <td><?php the_title(); ?></td>
                            <td><?php 
                                $product = wc_get_product( $product_id );
                                echo $product->get_regular_price();
                             ?></td>
                            <td style="text-align: center"><?php 
                            if($count == $round){
                                echo get_user_meta(get_current_user_ID(), 'monthly_due', true);
                            }
                            ?></td>
                            <td style="text-align: center"><?php 
                                if ( wc_customer_bought_product( wp_get_current_user()->user_email, get_current_user_id(), $product_id ) ) {
                                    ?>
                                        <a style="padding: 5px 10px; background: #a9a5aa;border-radius: 5px;color: #fff;box-shadow: 1px 1px 3px 3px #e8e8e8;" href="#">Paid</a>
                                    <?php
                                }
                                else{
                                     // get the product based on the ID
                                    $product = wc_get_product( $product_id );
    
                                    // get the "Checkout Page" URL
                                    $checkout_url = wc_get_checkout_url();
                                    ?>
                                    <a style="padding: 5px 10px; background: #009913;border-radius: 5px;color: #fff;box-shadow: 1px 1px 3px 3px #60ff78;" href="<?php echo $checkout_url.'?add-to-cart='.$product_id ?>">Pay Now</a>
                                <?php
                                }
                            ?>
                            </td>
                        </tr>
    
                    <?php

                    }
                    echo paginate_links(array(
                        'total' => $products->max_num_pages           // custom query pagination total number of pages req
                    ));
                }
                wp_reset_postdata();
            ?> 

               </tbody>
           </table>
        </div><br><br><hr>
        <?php
                   }

                   ?>
        <div>
       
        <br>
        <br>
        <br>
        <div style="text-align: center;box-shadow: 7px 7px 7px 0px #51c040;padding: 10px;border-radius: 10px;background: #009936;color: #bfffa5; margin-bottom: 10px;"><b> Lifetime Membership : </b></div>
                <table style="border-spacing: 5px;border-collapse: separate; width:100%;" class="table table-hover">
                    <thead>
                        <tr>
                            <th style="text-align: center">Description</th>
                            <th>Price</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="box-shadow: 1px 2px 5px -1px #959595;">
                        <?php
                            if( get_user_meta(get_current_user_ID(), 'lifetime_member', true) == 'no'){
                                $title = 'LifeTime-Membership';
                                $query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title = '$title' AND `post_status` = 'publish' AND `post_type` = 'product'";
                                $product_id = $wpdb->get_var($query);
                                $product = wc_get_product( $product_id );
                                // print_r($product);
                                // echo $d = get_user_meta(get_current_user_ID(), 'lifetime_member', true);
                                $product_price = $product->get_regular_price();
                                // get the "Checkout Page" URL
                                $checkout_url = wc_get_checkout_url();
                                ?>
                                <td style="text-align: center">Get Lifetime Membership Now</td>
                                <td><?php echo $product_price.'Tk'; ?></td>
                                <td><a style="padding: 5px 10px; background: #009913;border-radius: 5px;color: #fff;box-shadow: 1px 1px 3px 3px #60ff78;" href="<?php echo $checkout_url.'?add-to-cart='.$product_id ?>">Buy Now</a></td>
                                <?php
                            }
                            else{
                                $d = get_user_meta(get_current_user_ID(), 'lifetime_member', true);
                                ?>
                                <td style="text-align: center">Get Lifetime Membership Now</td>
                                <td>purchased</td>
                                <td><a style="padding: 5px 10px; background: #a9a5aa;border-radius: 5px;color: #fff;box-shadow: 1px 1px 3px 3px #e8e8e8;" href="#" disabled>Already purchased</a></td>
                                <?php
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
                <div>
                    <a href="<?php echo site_url('/my-account/club-members/'); ?>" style="padding: 5px 10px; background: #a9a5aa;border-radius: 5px;color: #fff;box-shadow: 1px 1px 3px 3px #e8e8e8;">See Member List</a>
                </div>
            </div>
   <?php

}


add_action('init', 'mohaazon_club_payment_menu_link2');
function mohaazon_club_payment_menu_link2(){
    add_rewrite_endpoint('club-members', EP_ROOT | EP_PAGES);
}


add_filter('woocommerce_get_query_vars', 'mohaazon_club_payment_query_vars2', 0);
function mohaazon_club_payment_query_vars2($vars){
    $vars[] = 'club-members';
    return $vars;
}


add_filter('woocommerce_account_menu_items','mohaazon_club_payment_menu_items2');
function mohaazon_club_payment_menu_items2($items){
    $new = array( 'club-members' => 'Members' );
    $items = array_slice( $items, -1, 0, true ) + $new + array_slice( $items, 0, NULL, true );
 
	return $items;
}


add_action( 'woocommerce_account_club-members_endpoint', 'mohaazon_club_payment_menu_items_page22' );
function mohaazon_club_payment_menu_items_page22(){
  
?>
               <div id="members-list-section" style="width: 80%; margin: auto">
                <table>
                <?php
                    $new = array(
                        'Md.Ridhwanul Haq',
                        'Syed Farhad Anwar',
                        'Md.Rezaul Kabir',
                        'MIRZA SADRUL ALAM',
                        'SAIF IFTEKHAR MAHMOOD',
                        'B.M. KAMAL HOSSAIN',
                        'MD.RAHAT KHAN',
                        'JAMES JACOB RIBERO',
                        'JOYDEEP CHOUDHURY',
                        'Shariful Islam',
                        'ABUL BASHAR MOHAMMED FAKHRUZZMAN',
                        'Syed Fazle Naiaz',
                        'MD. JUBAIR AHMED',
                        'Saeed Ahmed',
                        'Nadia Haq',
                        'Syed Khairul Hassan',
                        'Monalisa Mannan',
                        'Mohammad Tanvir Hydar Pavel',
                        'Neaz Ahmed',
                        'Afzalur Rahman Choudhury',
                        'Akhlasur Rahman Bhuiyan',
                        'Dr.Nadia Binte Amin',
                        'Ziaur Rahman',
                        'Muhammad Aref Mesbahuddin',
                        'M. Nazeem A. Choudhury',
                        'Gazi Mahfuzur Rahman',
                        'Md. Kausar Alam',
                        'Mohammad Abid Hassan',
                        'Arshadur Rahman Khan',
                        'Ziaur Rahman',
                        'Farzana Sharmeen',
                        'Wakar Hasan',
                        'Abu Yousuf Md. Abdullah',
                        'Md. Fakhrul Alam',
                        'Rozina Aliya Ahmed',
                        'Kazi Mahbub Hassan',
                        'Tareq Ul Islam',
                        'Nusrat Jabeen',
                        'Tahsin Taher',
                        'Saadi Manzoorul  Huq',
                        'AZM Masunur Rahman',
                        'Moin Al Kashem',
                        'Nabil Mustafizur Rahman',
                        'Dewan Sazzadul Karim',
                        'Syed Akhlakuzzaman',
                        'Hanif Mahtab',
                        'Rashed Kamal',
                        'Syed Abul Basher Tahmeed',
                        'Mohammed Tanvir Zubair Ahmed',
                        'Muhammad Rashedur Rahman',
                        'Khondaker Abul Faiz Md. Mohibulla',
                        'Faisal Quayyum',
                        'Mohammad Habib Rashid',
                        'A.S.M Rafiq Ullah',
                        'Quazi Habibul Hossain',
                        'Md. Arup Haider',
                        'Abul Kasem Md. Sadeque Nawaj',
                        'Naser Syed Salahuddin Abu',
                        'MD. Mohsin Habib Chowdhury',
                        'Abu Issa Mohammad Mainuddin',
                        'Yasir Azman',
                        'Md. Hasib Ahmed',
                        'Saquib Shahriar',
                        'Prakash Kanti Das',
                        'Shaymal Barman',
                        'Mohammad Asif Mahfuz',
                        'Gopi Kishon Sureka',
                        'Md. Anwar Hossain',
                        'Jesmin Ehsan',
                        'Mehnaz Kabir',
                        'Dr. Samiul Parvez Ahmed',
                        'Shobhon Mahbub Shahabuddin (Raj)',
                        'Kazi Mushfiqur Rahman',
                        'Sheehan Abdullah Al Husain',
                        'Nafees Anwar',
                        'Ehsanul Karim',
                        'Tanvira Choudhury',
                        'Muhammad Risalat Siddique',
                        'Armaan Ahsan Khondokar',
                        'Ghalib Haider',
                        'Md. Mahbub Alam',
                        'Prasenjit Chakma',
                        'Shafeen Ibtesam Nasir',
                        'Md. Enayet Ullah Khan',
                        'Firoz Ahmed Khan',
                        'Niaz Iqbal Shujat',
                        'Umana Anjalin',
                        'Fahmida Sharmeen',
                        'Md. Aziz Sultan',
                        'Makam E Mahmud Billah',
                        'Mohammad Anis',
                        'Quazi Mahbub Murshed',
                        'Muhammad Abdullah Ibrahim',
                        'Zahid Hassan Khan',
                        'Mir Nawbut Ali',
                        'ATM Shamim Uz Zaman',
                        'Moasser Ahmed',
                        'Wajiha Reza',
                        'Ahmed Tausif Saad',
                        'Malik M. Sayeed',
                        'Rezwanur Rab Zia',
                        'Shovan Chakraborty',
                        'Nafis Raihan',
                        'Faisal Sharif',
                        'Syed Naimul Abedin',
                        'Asma Ali',
                        'Syed Sadeque Mohammad',
                        'Mohammed Azharul Huq',
                        'Mahbubul Azam Bhuiyan',
                        'Md. Munir Chowdhury',
                        'Farman Rahman Chowdhury',
                        'Syed Tamjid Ur Rahman',
                        'Afzalul Hasan Khan (Maruf)',
                        'Quazi Mahmud Ahmed',
                        'Firoze Muhammad Zahidur Rahman',
                        'Mahbubul Matin',
                        'Faiyead Ahmedul Hye',
                        'Md. Shaifullah Khaled Shams',
                        'Ahmad Sajid',
                        'Mohammad Ali Nawaz',
                        'Imran Rahman',
                        'Md. Arman Rashid',
                        'M Hafizul Huq Babar',
                        'A.B.M. Mazharul Islam',
                        'Mohammad Shariful Hassan',
                        'SK. Talibur Rahman',
                        'Syed Mahbubur Rahman',
                        'Redwanur Rahman Chowdhury',
                        'Shafqat Ahmed',
                        'Shaery Aziz',
                        'Gitanka Debdip Datta',
                        'Muhammad Mohsin Uddin',
                        'Rozana Wahab',
                        'Shoeb Ahmed Masud',
                        'Hasan Ahmed Chowdhury',
                        'Kazi Sarjil',
                        'Muhammad Ali Talukder',
                        'Syed Arifuzzaman',
                        'Syed Morshed Kamal',
                        'Mohammed Shahriar Alam',
                        'Mohammad Modasser Pasha',
                        'Muhammad Sayeed',
                        'Abhijit Chowdhury',
                        'Md. Habibur Rahman Akand',
                        'Moshfeq Ullah Rafiq',
                        'Foyez Ahmed',
                        'Mohammad Fahmid Islam',
                        'Salmina Azmi',
                        'Rowena Afreen',
                        'Jotika Hossain',
                        'Bidhan Sarker',
                        'Isham Ul Haque',
                        'Nadia Afrin',
                        'Shamarukh Fakhruddin',
                        'Meer Mahbub Mostafa Ali',
                        'Abhishek Paul',
                        'Md. Morshed ul Arefin',
                        'Gazi Yar Mohammed',
                        'Md. Fazlul Hoque',
                        'M.A. Bani Amin',
                        'Naimul Hassan',
                        'Asif Ashraf',
                        'Mohammad Arfan Ali',
                        'Md. Mofazzal Hossain',
                        'Selim Abed',
                        'Arshad Mahmood',
                        'Nazim Uddin Ahmed',
                        'Sitara Abedin',
                        'Md Alamgir Hossain',
                        'Saleh Muzahid',
                        'Mohammad Zillur Rahman',
                        'Jamal Ahmed Chowdhury',
                        'Md. Zahid Hossain',
                        'Md. Shah Imran',
                        'Sabbir Hossain',
                        'Junaid Ahsan',
                        'Jamil Ahmed',
                        'Mamunul Hoque',
                        'Sheikh Abul Hashem',
                        'Mohammad Shakil Wahed',
                        'S H Aslam Habib',
                        'Imtiaz Ahmad Shams Newton',
                        'Sayeed Noor-Us-Salam',
                        'Solaiman Alam',
                        'Farhad Ahmed Khan',
                        'Md. Zahidul Islam',
                        'Mr. Selim Reza Farhad Hussain',
                        'Muhit Rahman',
                        'Mushfique Manzoor',
                        'Abrar Alam Anwar',
                        'Mustafizur Rahman Shazid',
                        'Taslim Ahmed Russell',
                        'Sami Ashraf',
                        'Md. Amanur Rahman',
                        'Md.Kamruzzaman ( Biplob)',
                        'Md.Shahid Ullah',
                        'Jashim Uddin',
                        'Tawfiq Ali',
                        'Abida Ali',
                        'Tahsina Banu',
                        'Zeenat Harun Choudhury',
                        'Kazi Shafqat Karim',
                        'Tanmi Haque',
                        'Abdullah Al Arif',
                        'M Nakibur Rahman',
                        'Sarder Swaket Ali',
                        'Mahtab Osmani',
                        'Shakil Rahman',
                        'Khaled Mamud',
                        'Rana Mohammad Sohail',
                        'Syed Mahbubul  Haque Chowdhury',
                        'Mohammad Monir Uddin',
                        'Musihul Huq Chowdhury',
                        'Farid Sikder',
                        'Md Farhan Imtiaz',
                        'Md Tashikul Alam',
                        'Md.Jalalul Azim',
                        'Tamzida Karim',
                        'Sohel Ahmed',
                        'Azfar Ar Adib',
                        'Nashir Uddin Ahmed Khan',
                        'Md Rafiqu Islam',
                        'Md Monjur Iqbal',
                        'Asif Zaman',
                        'Shammi Rubayet Karim',
                        'Ishtiaq Hussen Chowdhury',
                        'Sudipta Husain',
                        'Md Amanullah',
                        'Md.Ahmadul Haque',
                        'Riyad Siddiqui',
                        'Kashfia Tabassum Ahmed',
                        'Siam Haque',
                        'Sabrina Sarwar Islam',
                        'Sayeeful Islam',
                        'K M Zahirul Quayum',
                        'Nuzhat Tabassum Ahmed',
                        'Javed Mahmud',
                        'NAZMUL HASSAN',
                        'JOHORA BEBE',
                        'M MUSLEH UZ ZAMAN',
                        'M RAFIQUL ISLAM',
                        'MD. AN-NURUL MASUD',
                        'MD KAMRUL AHSAN',
                        'ASIF MOHAMMED TOUHID',
                        'EISHITA  ALAM',
                        'SHEIKH SHABAB AHMED',
                        'RANA JAVED KABIR',
                        'MOHAMMAD SALAHUDDIN CHOWDHURY',
                        'Mohammed Khorshed Alam',
                        'Mohammad Mamdudur Rashid',
                        'Md. Sydur Rahman',
                        'Iftekhar Ahmed Zafar',
                        'Kahirun Nahar Haque',
                        'Mohammod Moniruzzaman Khan',
                        'Syed Javed Noor',
                        'Shaheduzzaman Choudhury',
                        'Mohammad Shahnour Alam',
                        'Md. Nayim Chowdhury',
                        'Naser Ezaz Bijoy',
                        'Mohammad Mukhlesur Rahman',
                        'Imran Ahmed',
                        'Salman Hossain Khan',
                        'Alamgir Morshed',
                        'Zakir Ibne Hai',
                        'Ahmed Abu Insaf',
                        'Mahmudur Rahman',
                        'M Naimul Basher Chowdhury',
                        'Md. Ariful Islam',
                        'Afsar Uddin Ahmed',
                        'Iftekhar Ahmad',
                        'Rizvi Ul Kabir',
                        'Khandkar Obaidul Hasan',
                        'Kazi Saiful Hoque',
                        'Md. Mohiuddin',
                        'Dr. Md. Riad Mamun Prodhani',
                        'Md. Saif Noman Khan',
                        'Shehzad Munim',
                        'MD. Fazlul Hoque',
                        'Tanvir Shovan Haider Chaudhury',
                        'Sajjid Alam',
                        'Ireen Akhter',
                        'Aslam Khaleel',
                        'Md Asad Ur Rahman',
                        'Narmin Tartila Banu',
                        'S M Mahmud Hussain',
                        'M M Rawnak Eunus',
                        'Dr.Mohammad Naveed Ahmed',
                        'Abu Shahriar Zahedee',
                    );
                    $count=0;
                    ?>
                    <table>
                    <tr>
                        <th style="width: 30%">Serial</th>
                        <th style="width: 70%">Name</th>
                    </tr>
                    <?php
                    foreach($new as $item){
                        $count++;
                        ?>
                            <tr>
                                <td style="text-align: left"><?php echo $count ?></td>
                                <td style="text-align: left"><?php echo $item ?></td>
                            </tr>

                        <?php
                    }
                    ?>
                </table>
            </div>
<?php
}


       