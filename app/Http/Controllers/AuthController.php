use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

public function login(Request $request)
{
    $credentials = $request->only('username','password');

    if (Auth::attempt($credentials)) {
        return response()->json([
            'message' => 'Login success'
        ]);
    }

    return response()->json([
        'message' => 'Login failed'
    ],401);
}