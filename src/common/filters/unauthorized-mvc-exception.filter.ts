import {
	ExceptionFilter,
	Catch,
	ArgumentsHost,
	HttpException,
	UnauthorizedException,
} from '@nestjs/common';
import { Response } from 'express';

@Catch(UnauthorizedException)
export class UnauthorizedMvcExceptionFilter implements ExceptionFilter {
	catch(_: HttpException, host: ArgumentsHost) {
		const ctx = host.switchToHttp();
		const response = ctx.getResponse<Response>();

		response.redirect('/auth/login');
	}
}
