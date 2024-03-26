<?php
class ExpenseController extends BaseController
{
    function getImageBase64FileExt($base64) {
        // $base64 = 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAPYA…g4ODg4ODg4ODg4DB//P+x99hmgz+VBwAAAABJRU5ErkJggg==';
        $ftags = explode(';', $base64);
        $posType = strpos($ftags[0], "/");
        return substr($ftags[0], $posType+1);
    }

    function saveFile($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' ); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );
    
        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
        // clean up the file resource
        fclose( $ifp ); 
    
        return $output_file; 
    }

    public function addExpense($company, $reqParams){
        $strErrorDesc = '';
        // $arrQueryStringParams = $this->getQueryStringParams();
        // echo "addExpense\n";
        $documents = "";

        // $files = explode(",", $reqParams["expenseData"]["documents"]);
        $files = $reqParams["expenseData"]["documents"];
        // print_r($reqParams["expenseData"]["documents"]);

        $docDate = preg_replace('/-|\s|:/', "", $reqParams["expenseData"]["itemDate"]); //str_replace("-", $reqParams["expenseData"]["itemDate"])
        $prefix = "{$company}_{$reqParams["expenseData"]["employeeId"]}_expense_";

            for($i = 0;$i < count($files);$i++) {
                $fileName = sprintf('%s%s_%03d.%s', $prefix, $docDate, $i+1, $this->getImageBase64FileExt($files[$i]));
                $documents .= ($fileName.",");
                // echo "../app_data/".$fileName."\n";
                $this->saveFile($files[$i], "../../../app_data/expenses/".$fileName);
            }
        if(strlen($documents)) {
            $documents = rtrim($documents, ",");
        }

        // echo $documents;
        // echo "\n";
        // OWL_OWL0001_expense_202311270800_001.jpeg,OWL_OWL0001_expense_202311270800_002.png
        try {
            $model = new ExpenseModel();
            $responseData = $model->addExpense($company, 
                    Array(
                        "employee_id" => $reqParams["expenseData"]["employeeId"],
                        "item_date" => $reqParams["expenseData"]["itemDate"],
                        "amount" => $reqParams["expenseData"]["amount"],
                        "detail" => $reqParams["expenseData"]["detail"],
                        "documents" => $documents, //$reqParams["expenseData"]["documents"],
                    )
            );
        } catch (Error $e) {
            print_r($e);
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }
        return true;
    }

    public function getExpenses($company, $reqParams, $limit=0){
        $strErrorDesc = '';
        // echo "getExpenses controller";
        try {
            $expenseModel = new ExpenseModel();
            $responseData = $expenseModel->getExpenses($company, $reqParams, $limit);
        } catch (Error $e) {
            echo $e;
            $strErrorDesc = $e->getMessage();
        }
        // print_r($responseData);
        if (!$strErrorDesc) {
            // employee_id, username, email, fullname, gender, birthdate, enroll_date, contact_no, contact_person, role, department
            return array_map(function($item) {
                return Array(
                    "recId" => $item["rec_id"],
                    "employeeId" => $item["employee_id"],
                    "company" => $item["company"],
                    "fullname" => $item["fullname"],
                    "itemDate" => $item["item_date"],
                    "detail" => $item["detail"],
                    "amount" => $item["amount"],
                    "documents" => $item["documents"],
                    "status" => $item["status"],
                    "approvedBy" => $item["approved_by"],
                    "approveNote" => $item["approve_note"],
                    "approveDate" => $item["approve_date"],
                    "createDate" => $item["create_date"],
                );
            
            }, $responseData);
        } else {
            return Array("error"=>$strErrorDesc);
        }
    }

    public function updateExpense($conditions, $updateData){
        $strErrorDesc = '';

        try{
            
            $model = new ExpenseModel();
            $responseData = $model->updateExpense($conditions, $updateData);
        } catch (Error $e) {
            // print_r($e->getMessage());
            $strErrorDesc = $e->getMessage();
            return Array("error"=>$strErrorDesc);
        }

        return $responseData;
    }
}
