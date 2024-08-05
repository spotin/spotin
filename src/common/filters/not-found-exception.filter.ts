import {
	ExceptionFilter,
	Catch,
	ArgumentsHost,
	HttpException,
	NotFoundException,
} from '@nestjs/common';
import { Response } from 'express';

@Catch(NotFoundException)
export class NotFoundExceptionFilter implements ExceptionFilter {
	catch(_: HttpException, host: ArgumentsHost): void {
		const ctx = host.switchToHttp();
		const response = ctx.getResponse<Response>();

		response.redirect('/not-found');
	}
}
