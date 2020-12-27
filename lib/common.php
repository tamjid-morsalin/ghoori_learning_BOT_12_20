<?PHP
// Release note: Wraps up database connections and DB access functions - connect/query/result etc.
// Version: 1.0.1
// Date: August 19, 2011
// Author: Minaoar Hossain Tanzil
/*
 *Version: 1.0.2
 *Upadate: October 2,2017
 *User: Al Amin
 * Release note: mysqli database connections type Added.
 *  */


function connectDB()
{
    global $dbtype;
    global $Server;
    global $Database;
    global $UserID;
    global $Password;


    if ($dbtype=="odbc")
    {
        $cn=odbc_connect("Driver={SQL Server};Server=$Server;Database=$Database","$UserID", "$Password");
        if(!$cn)
            die("err+db connection error");
        else
            return $cn;

        return $cn;
    }
    else if($dbtype=="mssql")
    {
        $cn=mssql_connect("$Server","$UserID", "$Password");
        $ret=mssql_select_db($Database);

        if(!$cn)
            die("err+db connection error");
        else
            return $cn;

        return $cn;
    }
    else if($dbtype=="mysqli")
    {
        $cn = mysqli_connect($Server,$UserID, $Password, $Database);
        if(!$cn)
        {
            die("err+db connection error: " . mysqli_connect_error());
        }
        else
            return $cn;

        return $cn;
    }
    else
    {
        $cn=mysql_connect($Server,$UserID,$Password);
        mysql_select_db($Database);

        if(!$cn)
            die("err+db connection error: $Server,$UserID,$Password");
        else
            return $cn;

        return $cn;
    }
}
/*
 * close database connection
 *  */
function ClosedDBConnection($cn)
{
    global $dbtype;
    if($dbtype == 'odbc')
        odbc_close($cn);
    else if($dbtype == 'mssql')
        mssql_close($cn);
    else if($dbtype == 'mysqli')
        mysqli_close($cn);
    else
        mysql_close();
}
/*
 * execution  query
 *  */
function Sql_exec($cn,$qry)
{
    global $dbtype;

    if($dbtype == 'odbc')
    {
        $rs=odbc_exec($cn,$qry);
        if(!$rs)
            die("err+".$qry);
        else
            return $rs;
    }
    else if($dbtype == 'mssql')
    {
        $rs=mssql_query($qry, $cn);

        if(!$rs) {
            echo(mssql_get_last_message());
            die("err+".$qry);
        }
        else
            return $rs;
    }
    else if($dbtype == 'mysqli')
    {
       // mysqli_query($cn,'SET CHARACTER SET utf8');
       // mysqli_query($cn,"SET SESSION collation_connection ='utf8_general_ci'");
        $rs=mysqli_query($cn,$qry);
        if(!$rs)
            die("err + $qry:".mysqli_error($cn));
        else
            return $rs;
    }
    else
    {
        $rs=mysql_query($qry,$cn);
        if(!$rs)
            die("err+".$qry);
        else
            return $rs;
    }
}
/*
 * fetch array to extract query result for select
 *   */
function Sql_fetch_array($rs)
{
    global $dbtype;
    if($dbtype == 'odbc')
        return odbc_fetch_array($rs);
    else if($dbtype == 'mssql')
        return mssql_fetch_array($rs);
    else if($dbtype == 'mysqli')
        return mysqli_fetch_array($rs);
    else
        return mysql_fetch_array($rs);
}
function Sql_fetch_assoc_array($rs)
{
    global $dbtype;
    if($dbtype == 'odbc')
        return odbc_fetch_array($rs);
    else if($dbtype == 'mssql')
        return mysql_fetch_assoc($rs);
    else if($dbtype == 'mysqli')
        return mysqli_fetch_assoc($rs);
    else
        return mysql_fetch_array($rs);
}
/*
 * return data of a index. input result and column name and return data of this index
 *  */
function Sql_Result($rs,$ColumnName)
{
    global $dbtype;

    return $rs[$ColumnName];
}
/*
 *return number of rows
 *  */
function Sql_Num_Rows($result_count)
{
    global $dbtype;
    if($dbtype == 'odbc')
        return odbc_num_rows($result_count);
    else if($dbtype == 'mssql')
        return mssql_num_rows($result_count);
    else if($dbtype == 'mysqli')
        return mysqli_num_rows($result_count);
    else
        return mysql_num_rows($result_count);

}

function Sql_GetField($rs,$ColumnName)
{
    global $dbtype;

    if($dbtype == 'odbc')
        return odbc_result($rs, $ColumnName);
    else if($dbtype == 'mssql')
        return mssql_result($rs, 0, $ColumnName);
    else if($dbtype == 'mysqli')
    {
        $row = mysqli_fetch_assoc($rs);

        return $row[$ColumnName];
    }
    else
        return mysql_result($rs, 0, $ColumnName);
}
/*
 *free rrsult
 *  */
function Sql_Free_Result($rs)
{
    global $dbtype;

    if($dbtype == 'odbc')
        return odbc_free_result($rs);
    else if($dbtype == 'mssql')
        return mssql_free_result($rs);
    else if($dbtype == 'mysqli')
        return mysqli_free_result($rs);
    else
        return mysql_free_result($rs);
}
/*====================================ADD function for SQL Select Query=================================================
 * parameters
 * 1st connection $data['cn'] not empty.
 * 2nd table name ex- $data['tableName']=" table ";$data['tableName']=" table inner join table2 on table.id=table2.userid ";   not empty.
 * 3rd column name ex- $data['columnName']=" * "; $data['columnName']=" count(*), col1,col2 ";
 * 4th where cluse ex- $data['whereClause'] = "col='3' "; $data['whereClause'] = " salary > '100.0' and increment= '10%' ";
 * 5th group by ex- $data['groupBY'] =" id "$data['groupBY'] =" id , name";
 * 6th order by  ex-$data['orderBY']=" id "; $data['orderBY']="id desc , name asc ";
 * 7th limit ex- $data['limiteOfSet']=" 10 ";$data['limiteOfSet']=" 0,10 ";
 * only mentatory field is $cn and $tableName other field is optional
 * $data[cn]=$cn;
 * $data[tableName]="test";
 *=================================================================================================================  */
function Sql_select($data)
{

    if(!empty($data['cn'])&&!empty($data['tableName'])){
        if(empty($data['columnName'])){
            $data['columnName']=" * ";
        }
        $qry="select ".$data['columnName']." from ".$data['tableName'] ;
        if(!empty($data['whereClause'])){
            $qry=$qry." where ".$data['whereClause'] ;
        }
        if (!empty($data['groupBY'])) {
            $qry=$qry." group by ".$data['groupBY'] ;
        }
        if (!empty($data['orderBY'])) {
            $qry=$qry." order by ".$data['orderBY'] ;
        }
        if (!empty($data['limiteOfSet'])) {
            $qry=$qry." limit ".$data['limiteOfSet'] ;
        }
        $cn=$data['cn'];

        return Sql_exec($cn,$qry);
    }
    else {
        return "Error :connection or table name empty  ";
    }
}
/*===================================================================================================================
 * End  function for SQL Select Query
 *==================================================================================================================  */
/*=================================================================================================================
 * start http request function
 *=================================================================================================================  */
function request_http_fun($index_str){

    $index_str=(string)$index_str;
    if(isset($_REQUEST[$index_str])){
        if(!empty($_REQUEST[$index_str])){
            $return_string=$_REQUEST[$index_str];
        }else{
            $return_string="NULL";
        }
    }  else {
        $return_string="NULL";
    }
    return $return_string;
}

function sql_con_exec_close($query, $conDB,$closeDB)
{
 if($conDB<>false){
     global $cn;
     $cn = connectDB();
 }

    $result = sql_exec($cn, $query);

    if ($closeDB <> false) {
        ClosedDBConnection($cn);
        return $result;
    }

    return $result;

}
/*=================================================================================================================
 * end http request function
 *=================================================================================================================  */

?>