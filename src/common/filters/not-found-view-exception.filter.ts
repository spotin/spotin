import { User } from '@/users/types/user';
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
		// TODO: This does not work at the moment. Interceptor might be a better solution.
		// It is kept here for future investigation.
		const user = request.user as User | undefined;

		response.render('not-found', {
			username: user?.username,
			email: user?.email,
			role: user?.role,
			title: 'ui.notFound.title',
			description: 'ui.notFound.description',
			url: `${request.protocol}://${request.get('host')}${request.originalUrl}`,
		});
	}
}
