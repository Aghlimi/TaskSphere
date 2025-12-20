<!DOCTYPE html>
<html >
    </head>
    <body >
        <h1 > An error occurred: {{ $message }} </h1 >
        <button>show details</button>
        <p></p>
        <script>
            let v = false;
            const button = document.querySelector('button');
            button.onclick =  () => {
                v = !v;
                if(v) document.querySelector('p').innerText = `{{ $details }}`;
                else document.querySelector('p').innerText = ``;
            }
        </script>
    </body>
</html>
