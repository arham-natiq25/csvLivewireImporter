<?php

namespace App\Livewire;

use App\Helpers\ChunkIterator;
use App\Jobs\ImportCsv;
use Illuminate\Support\Facades\Bus;
use League\Csv\Reader;
use League\Csv\Statement;
use Livewire\Component;
use Livewire\WithFileUploads;

class CsvImporter extends Component
{
    use WithFileUploads;
    public bool $open = true;

    public $file;
    public string $model; // string use to bind with model

    public array $fileHeaders = []; // all headers like id,name,email

    public array $columnsToMap = []; // colums we want to map from database like id , name , email that are in database
    public array $requiredColumns = []; // colums that are required
    public array $columnLabels = []; // colums that are required
    protected $listeners = [
        'toggle'
    ];

    function mount() {
        $this->columnsToMap = collect($this->columnsToMap)
        ->mapWithKeys(fn ($column) =>[$column=>''] )
        ->toArray();
    }
    function rules() {
        $columnRules = collect($this->requiredColumns)->mapWithKeys(function ($column) {
            return ['columnsToMap.'.$column=>['required']];
        })->toArray();



        return array_merge( $columnRules ,[
            'file'=>['required','mimes:csv','max:51200'],
        ]);
    }

    function validationAttributes() {
        return collect($this->requiredColumns)->mapWithKeys(function ($column) {
           return ['columnsToMap.'.$column=> strtolower($this->columnLabels[$column] ?? $column)];
        })->toArray();
    }
    public function updatedFile() {
        // valudate

        $this->validateOnly('file');

        //read the csv

       $csv = $this->readCsv;

       $this->fileHeaders = $csv->getHeader();


        // grap data from csv
    }



    public function getReadCsvProperty():Reader{
       return $this->readCsv($this->file->getRealPath());
    }
    public function getCsvRecordsProperty() {
        return Statement::create()->process($this->readCsv);
    }




    function import() {
        $this->validate();

        // create a new import record here

       $batches = collect( (new ChunkIterator($this->csvRecords->getRecords(),10))->get())->map(function ($chunk) {
         return new ImportCsv();
       })->toArray();


       Bus::batch($batches)->dispatch();
    //    \Bus::batch

    //    $abc=[];

    //    foreach($chunks as $chunk)
    //    {
    //     $abc[]= $chunk;
    //    }

    //    dd($abc);
        $this->createImport();


    }


    public function createImport() {

        return auth()->user()->imports()->create([
            'file_path' =>$this->file->getRealPath(),
            'file_name' =>$this->file->getClientOriginalName(),
            'total_rows'=>count($this->csvRecords),
            'model'=>$this->model,


        ]);
    }

    protected function readCsv(string $path) :Reader {
        $stream = fopen($path,'r');

        $csv = Reader::createFromStream($stream);

        $csv->setHeaderOffset(0);

        return $csv;
    }

    public function toggle() {
        $this->open = !$this->open;
    }
    public function render()
    {
        return view('livewire.csv-importer');
    }
}
