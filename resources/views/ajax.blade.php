{{-- import  --}}

    {{-- dashboard  --}}
         {{-- get_ajax_top_usd --}}
        DB::select("select `importer_name`, ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd from `importer_bills` where YEAR(bill_of_entry_date) = $year group by `importer_name` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0");
        
        {{-- get_ajax_top_usd_port --}}
        DB::select("select indian_port AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2)  AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = $year group by `indian_port` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0");

        {{--get_ajax_top_usd_country --}}
        DB::select("select `origin_country`, ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd from `importer_bills` where YEAR(bill_of_entry_date) = $year group by `origin_country` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0");

    {{-- impoter Analysis  --}}
        {{-- get_ajax_impana_usd_comp --}}
        DB::select("select `importer_name`, ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `importer_name` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0");
        {{-- get_ajax_impana_usd_cost --}}
        DB::select("select `importer_name`, ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `importer_name` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0");
        {{-- get_ajax_impana_usd_quantity --}}
        DB::select("select `importer_name`, ROUND(SUM(quantity), 2) AS top_quantity from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `importer_name` order by ROUND(SUM(quantity), 2) desc limit 15 offset 0");

    {{-- expoter analysis  --}}
        {{-- ga_imp_supana_usd_comp --}}
        DB::select("select `supplier`, ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `supplier` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0")

        {{-- ga_imp_supana_usd_cost --}}
        DB::select("select `supplier`, ROUND(SUM(total_value_usd_exchange), 2) AS top15_usd from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `supplier` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0")

        {{-- ga_imp_supana_usd_quantity --}}
        DB::select("select `supplier`, ROUND(SUM(quantity), 2) AS top_quantity from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `supplier` order by ROUND(SUM(quantity), 2) desc limit 15 offset 0")
    
    {{-- market Share  --}}
        {{-- ga_marketshare_cost_usd_port--}}
        DB::select("select indian_port AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `indian_port` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0")

        {{-- ga_marketshare_cost_qua_port --}}
        DB::select("select indian_port AS labeltitle, ROUND(SUM(quantity), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `indian_port` order by ROUND(SUM(quantity), 2) desc limit 15 offset 0")

        {{-- ga_marketshare_cost_qua_country --}}
        DB::select("select origin_country AS labeltitle, ROUND(SUM(quantity), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `origin_country` order by ROUND(SUM(quantity), 2) desc limit 15 offset 0")
        
        {{-- ga_marketshare_cost_usd_country --}}
        DB::select("select origin_country AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2021' group by `origin_country` order by ROUND(SUM(total_value_usd_exchange), 2) desc limit 15 offset 0")

    {{-- price Analysis --}}
        {{-- ga_priceana_usd_country --}}
        DB::select("select STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Sunday'), '%X%V %W') AS week_start, STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Saturday'), '%X%V %W') AS week_end, origin_country AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by YEARWEEK(bill_of_entry_date, 0) order by origin_country asc, YEARWEEK(bill_of_entry_date, 0) desc limit 100 offset 0")

        {{-- ga_priceana_usd_port --}}
        DB::select("select STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Sunday'), '%X%V %W') AS week_start, STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Saturday'), '%X%V %W') AS week_end, indian_port AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by YEARWEEK(bill_of_entry_date, 0) order by indian_port asc, YEARWEEK(bill_of_entry_date, 0) desc limit 100 offset 0")

        {{-- ga_priceana_usd_importer --}}
        DB::select("select STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Sunday'), '%X%V %W') AS week_start, STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Saturday'), '%X%V %W') AS week_end, importer_name AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by YEARWEEK(bill_of_entry_date, 0) order by importer_name asc, YEARWEEK(bill_of_entry_date, 0) desc limit 100 offset 0")

 {{-- comparison  --}}
    {{-- ga_comparison_usd_importer --}}
        DB::select("select STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Sunday'), '%X%V %W') AS week_start, STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Saturday'), '%X%V %W') AS week_end, importer_name AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by YEARWEEK(bill_of_entry_date, 0) order by importer_name asc, YEARWEEK(bill_of_entry_date, 0) desc limit 100 offset 0")

    {{-- ga_comparison_usd_country --}}
        DB::select("select STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Sunday'), '%X%V %W') AS week_start, STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Saturday'), '%X%V %W') AS week_end, origin_country AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by YEARWEEK(bill_of_entry_date, 0) order by origin_country asc, YEARWEEK(bill_of_entry_date, 0) desc limit 100 offset 0")

    {{-- ga_comparison_usd_ports--}}
        DB::select("select STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Sunday'), '%X%V %W') AS week_start, STR_TO_DATE(CONCAT(YEARWEEK(bill_of_entry_date, 0), ' ', 'Saturday'), '%X%V %W') AS week_end, indian_port AS labeltitle, ROUND(SUM(total_value_usd_exchange), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by YEARWEEK(bill_of_entry_date, 0) order by indian_port asc, YEARWEEK(bill_of_entry_date, 0) desc limit 100 offset 0")

{{-- Price compair --}}
    {{-- ga_pc_usd_country_max --}}
    DB::select("select `origin_country` as `labeltitle`, ROUND(MAX(unit_rate_in_usd), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by `origin_country` order by ROUND(MAX(unit_rate_in_usd), 2) desc limit 15 offset 0")


    {{-- ga_pc_qua_country_max --}}
    DB::select('select CONCAT(origin_country, '::', unit_quantity) AS labeltitle, ROUND(MAX(quantity), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by `unit_quantity` order by ROUND(MAX(quantity), 2) desc limit 15 offset 0')

    {{-- ga_pc_usd_country_min --}}
    DB::select("select `origin_country` as `labeltitle`, ROUND(MIN(unit_rate_in_usd), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by `origin_country` order by ROUND(MIN(unit_rate_in_usd), 2) desc limit 15 offset 0")

    {{-- ga_pc_qua_country_min --}}
    DB::select("select CONCAT(origin_country, '::', unit_quantity) AS labeltitle, ROUND(MIN(quantity), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by `unit_quantity` order by ROUND(MIN(quantity), 2) desc limit 15 offset 0")

    {{-- ga_pc_usd_port_max --}}
    DB::select("select `indian_port` as `labeltitle`, ROUND(MAX(unit_rate_in_usd), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by `indian_port` order by ROUND(MAX(unit_rate_in_usd), 2) desc limit 15 offset 0");

    {{-- ga_pc_qua_port_max--}}
    DB::select("select CONCAT(indian_port, '::', unit_quantity) AS labeltitle, ROUND(MAX(quantity), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by `unit_quantity` order by ROUND(MAX(quantity), 2) desc limit 15 offset 0");

    {{-- ga_pc_usd_port_min --}}
    DB::select("select `indian_port` as `labeltitle`, ROUND(MIN(unit_rate_in_usd), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by `indian_port` order by ROUND(MIN(unit_rate_in_usd), 2) desc limit 15 offset 0");

    {{-- ga_pc_qua_port_min --}}
    DB::select("select CONCAT(indian_port, '::', unit_quantity) AS labeltitle, ROUND(MIN(quantity), 2) AS labelvalue from `importer_bills` where YEAR(bill_of_entry_date) = '2020' group by `unit_quantity` order by ROUND(MIN(quantity), 2) desc limit 15 offset 0");


{{-- export  --}}

    {{-- dashboard --}}

    {{-- exp_get_ajax_top_usd --}}
    DB::select("select count(*) as aggregate from `exporter_bills` where `year` = $year group by `exporter`");

    {{-- exp_get_ajax_top_port --}}
    DB::select("select count(*) as aggregate from `exporter_bills` where `year` = $year group by `indian_port`");
    
    {{-- exp_get_ajax_top_usd_country --}}
    DB::select("select count(*) as aggregate from `exporter_bills` where `year` = $year group by `foreign_country`");