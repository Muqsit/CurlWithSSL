import type { NextApiRequest } from "next"

export const config = {
	runtime: "edge",
}

export default function handler(req: NextApiRequest) {
	const headers = {"Cache-Control": "public, max-age=604800"};

	if(req.method === "GET"){
		return new Response(JSON.stringify({
			name: "John Doe"
		}), {status: 200, headers: headers});
	}

	if(req.method === "POST"){
		return new Response(JSON.stringify({
			message: "Successfully updated profile information."
		}), {status: 200, headers: headers});
	}

	return new Response(null, {status: 400, headers: headers});
}
