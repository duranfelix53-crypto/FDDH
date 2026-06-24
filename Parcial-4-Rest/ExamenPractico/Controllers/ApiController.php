php
namespace Controllers;
use ModelsProductoModel;

class ApiController
{
    public function productos() void
    {
        header(Content-Type applicationjson; charset=UTF-8);
        header(Access-Control-Allow-Origin );
        header(Access-Control-Allow-Methods GET, OPTIONS);
        header(Access-Control-Allow-Headers Content-Type);

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $model = new ProductoModel();
        $productos = $model-obtenerTodos();

        http_response_code(200);
        echo json_encode([
            'success' = true,
            'data'    = $productos,
            'total'   = count($productos)
        ]);
    }
}
