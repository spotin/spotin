import {
	ExceptionFilter,
	Catch,
	ArgumentsHost,
	NotFoundException,
	HttpException,
} from '@nestjs/common';
import { PrismaClientKnownRequestError } from '@prisma/client/runtime/library';
import { Request, Response } from 'express';

@Catch(NotFoundException, PrismaClientKnownRequestError)
export class NotFoundViewExceptionFilter implements ExceptionFilter {
	catch(_: HttpException, host: ArgumentsHost): void {
		const ctx = host.switchToHttp();
		const request = ctx.getRequest<Request>();
		const response = ctx.getResponse<Response>();

		response.render('not-found', {
			url: request.protocol + '://' + request.get('host') + request.originalUrl,
		});
	}
}
